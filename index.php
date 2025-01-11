<?php
session_start();

// Reset to level 1 if requested
if (isset($_GET['reset'])) {
    $_SESSION['level'] = 1;
}

// Handle level progression before any other logic
if (isset($_GET['next'])) {
    $_SESSION['level'] = isset($_SESSION['level']) ? $_SESSION['level'] + 1 : 2;
    header('Location: index.php');
    exit;
}

// Get current level
$currentLevel = isset($_SESSION['level']) ? $_SESSION['level'] : 1;
$file = str_pad($currentLevel, 5, '0', STR_PAD_LEFT) . '.txt'; // Example: 00001.txt

// Read words from file
if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $pairs = array_map(function ($line) {
        return explode(';', $line);
    }, $lines);

    shuffle($pairs); // Shuffle pairs
    $leftWords = array_column($pairs, 0); // Left words
    $rightWords = array_column($pairs, 1); // Right translations
    shuffle($rightWords); // Shuffle translations
} else {
    $fileMissing = true; // Indicator that text file is missing
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matching game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .container {
            display: flex;
            justify-content: center;
            gap: 50px;
            flex-wrap: wrap;
        }
        .column {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .word {
            padding: 15px;
            border: 2px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            background-color: #fff;
            color: #333;
            font-weight: bold;
            text-align: center;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .selected {
            background-color: #e6f3ff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <h1>Level <?= $currentLevel ?></h1>

    <?php if (isset($fileMissing)): ?>
        <p>The txt file <strong><?= $file ?></strong> cannot be found. Please create this file to continue.</p>
        <button onclick="window.location.href='index.php?reset=true'">Back to level 1</button>
    <?php else: ?>
        <div class="container">
            <div id="left" class="column">
                <?php foreach ($leftWords as $index => $word): ?>
                    <div class="word" data-id="<?= $index ?>" onclick="selectWord(this, 'left')"><?= htmlspecialchars($word) ?></div>
                <?php endforeach; ?>
            </div>
            <div id="right" class="column">
                <?php foreach ($rightWords as $index => $word): ?>
                    <div class="word" data-id="<?= array_search($word, array_column($pairs, 1)) ?>" onclick="selectWord(this, 'right')"><?= htmlspecialchars($word) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <script>
        let selected = null;

        // Preload audio files
        const selectSound = new Audio('select.mp3');
        const matchSound = new Audio('match.mp3');

        function selectWord(element, side) {
            // Play selection sound
            selectSound.play();

            // Clear previous selection styling
            document.querySelectorAll('.word').forEach(word => word.classList.remove('selected'));

            // Handle new selection
            if (selected === null) {
                selected = { element, side };
                element.classList.add('selected');
                return;
            }

            // If two words from different columns are selected
            if (selected.side !== side) {
                if (selected.element.dataset.id === element.dataset.id) {
                    // Correct match: remove both words
                    selected.element.remove();
                    element.remove();

                    // Play match sound
                    matchSound.play();
                }
                selected = null;

                // Check if game is completed
                const remainingWords = document.querySelectorAll('.word');
                if (remainingWords.length === 0) {
                    // Use window.location.href to ensure PHP processes the level change
                    window.location.href = 'index.php?next=true';
                }
            } else {
                // If same column, update selection
                selected = { element, side };
                element.classList.add('selected');
            }
        }
    </script>
</body>
</html>

