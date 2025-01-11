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

// Get current level and total levels
$currentLevel = isset($_SESSION['level']) ? $_SESSION['level'] : 1;
$totalLevels = 0;
// Count total number of level files
for ($i = 1; ; $i++) {
    $testFile = str_pad($i, 5, '0', STR_PAD_LEFT) . '.txt';
    if (!file_exists($testFile)) {
        $totalLevels = $i - 1;
        break;
    }
}

$file = str_pad($currentLevel, 5, '0', STR_PAD_LEFT) . '.txt';

// Read words from file
if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $pairs = array_map(function ($line) {
        return explode(';', $line);
    }, $lines);

    shuffle($pairs);
    $leftWords = array_column($pairs, 0);
    $rightWords = array_column($pairs, 1);
    shuffle($rightWords);
} else {
    $fileMissing = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matching game</title>
    <style>
        :root {
            --primary-color: #4CAF50;
            --hover-color: #45a049;
            --selected-color: #e8f5e9;
            --border-color: #ddd;
            --text-color: #333;
            --shadow: 0 2px 4px rgba(0,0,0,0.1);
            --progress-bg: #e0e0e0;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            width: 100%;
            max-width: 600px;
        }

        h1 {
            color: var(--text-color);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .progress-container {
            width: 100%;
            background-color: var(--progress-bg);
            border-radius: 10px;
            height: 20px;
            margin-top: 1rem;
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }

        .progress-bar {
            height: 100%;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
            border-radius: 10px;
        }

        .progress-text {
            font-size: 0.9rem;
            color: var(--text-color);
            margin-top: 0.5rem;
        }

        .container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            max-width: 1200px;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .column {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            min-width: 200px;
            max-width: 400px;
        }

        .word-wrapper {
            transition: height 0.3s ease, margin 0.3s ease, padding 0.3s ease;
            height: auto;
        }

        .word {
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            background-color: white;
            color: var(--text-color);
            font-weight: 500;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            user-select: none;
            position: relative;
            height: auto;
        }

        .word:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .word.selected {
            background-color: var(--selected-color);
            border-color: var(--primary-color);
            transform: scale(1.02);
        }

        button {
            margin-top: 2rem;
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: bold;
            border: none;
            border-radius: 25px;
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        button:hover {
            background-color: var(--hover-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .container {
                gap: 1rem;
                padding: 10px;
            }

            .column {
                min-width: 120px;
            }

            h1 {
                font-size: 2rem;
            }

            .word {
                padding: 0.8rem;
                font-size: 0.9rem;
            }
        }

        .removing {
            animation: fadeOutAndCollapse 0.3s ease forwards;
        }

        @keyframes fadeOutAndCollapse {
            0% {
                opacity: 1;
                transform: translateY(0);
                height: var(--element-height);
                margin-bottom: 1rem;
            }
            100% {
                opacity: 0;
                transform: translateY(-10px);
                height: 0;
                margin-bottom: 0;
                padding-top: 0;
                padding-bottom: 0;
                border-width: 0;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Level <?= $currentLevel ?></h1>
        <div class="progress-container">
            <div class="progress-bar" style="width: <?= ($currentLevel / $totalLevels) * 100 ?>%"></div>
        </div>
        <div class="progress-text">Level <?= $currentLevel ?> of <?= $totalLevels ?></div>
    </div>

    <?php if (isset($fileMissing)): ?>
        <p>The txt file <strong><?= $file ?></strong> cannot be found. Please create this file to continue.</p>
        <button onclick="window.location.href='index.php?reset=true'">Back to level 1</button>
    <?php else: ?>
        <div class="container">
            <div id="left" class="column">
                <?php foreach ($leftWords as $index => $word): ?>
                    <div class="word-wrapper">
                        <div class="word" data-id="<?= $index ?>" onclick="selectWord(this, 'left')"><?= htmlspecialchars($word) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="right" class="column">
                <?php foreach ($rightWords as $index => $word): ?>
                    <div class="word-wrapper">
                        <div class="word" data-id="<?= array_search($word, array_column($pairs, 1)) ?>" onclick="selectWord(this, 'right')"><?= htmlspecialchars($word) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <script>
        let selected = null;
        let matchCount = 0;
        const totalPairs = <?= count($pairs) ?>;
        const selectSound = new Audio('select.mp3');
        const matchSound = new Audio('match.mp3');

        // Set initial heights
        document.querySelectorAll('.word').forEach(word => {
            const height = word.offsetHeight;
            word.closest('.word-wrapper').style.setProperty('--element-height', `${height}px`);
        });

        function selectWord(element, side) {
            selectSound.play();
            document.querySelectorAll('.word').forEach(word => word.classList.remove('selected'));

            if (selected === null) {
                selected = { element, side };
                element.classList.add('selected');
                return;
            }

            if (selected.side !== side) {
                if (selected.element.dataset.id === element.dataset.id) {
                    matchCount++;
                    
                    // Get wrapper elements
                    const wrapper1 = selected.element.closest('.word-wrapper');
                    const wrapper2 = element.closest('.word-wrapper');
                    
                    // Store heights before adding removing class
                    const height1 = wrapper1.offsetHeight;
                    const height2 = wrapper2.offsetHeight;
                    wrapper1.style.setProperty('--element-height', `${height1}px`);
                    wrapper2.style.setProperty('--element-height', `${height2}px`);

                    // Add removing class to wrappers
                    wrapper1.classList.add('removing');
                    wrapper2.classList.add('removing');

                    matchSound.play();

                    // Remove wrappers after animation
                    setTimeout(() => {
                        if (wrapper1.parentNode) wrapper1.remove();
                        if (wrapper2.parentNode) wrapper2.remove();

                        // Check if game is completed
                        const remainingWords = document.querySelectorAll('.word').length;
                        if (remainingWords === 0 || matchCount >= totalPairs) {
                            setTimeout(() => {
                                window.location.href = 'index.php?next=true';
                            }, 300);
                        }
                    }, 300);

                    selected = null;
                }
            } else {
                selected = { element, side };
                element.classList.add('selected');
            }
        }
    </script>
</body>
</html>
