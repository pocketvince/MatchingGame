# MatchingGame
![Alt text](https://raw.githubusercontent.com/pocketvince/MatchingGame/main/demo.gif?raw=true "demo")
Matching Game is a PHP script that allows pairing matches through a web interface.

## Description
The Matching Game script features a web interface where predefined pairs must be matched.

Users select items to pair, and the script automatically verifies if the matches are correct.

Validated pairs are removed from the list.

Once the list is completely cleared, the game proceeds to the next level.

## Setup
Place the following files in the root directory:

```shell
index.php
match.mp3
select.mp3
```
In the same directory, create text files named in the format 000001.txt, 000002.txt,...

Each file corresponds to a level, the script starts with 000001.txt and automatically proceeds to the next file once the level is completed.

If the next file is not found, the user will be prompted to return to level 1.

Text file structure:

Each line represents a pair to match, with two elements separated by a semicolon, example:

```shell
hello;world
bonjour;le monde
hallo;wereld
```

At launch, the script reads the text file corresponding to the current level and shuffles the pairs.

The left and right elements are displayed randomly, and the user must match them correctly.

Once all pairs are matched, the script moves to the next level.

## Todolist
⌛ Create a design for the interface.

✔️ Add sound effects.

⌛ Debug the issue with identical words.

## Extra
I was inspired by a small mobile game designed for practicing English.

As I’m currently learning Dutch, I looked for a similar tool but couldn’t find one.

So, I figured it wouldn’t be too difficult to create one in PHP (:

I downloaded lists of thousands of French-Dutch words and split them into text files (data available on GitHub).

This game can also be used to review any other type of pair associations.

Have fun!

## Contributing
Inspiration: https://play.google.com/store/apps/details?id=com.LanguageMonster.KarfarolGames&pcampaignid=web_share

Readme generator: https://www.makeareadme.com/
