# SS13 Tools
Tools for working with the [tgstation/tgstation](https://github.com/tgstation/tgstation) project's database and other fun things.

This is a refactor/rebuild of the original ss13 tools at [balohmatevz/SS13Tools](https://github.com/balohmatevz/SS13Tools)

## Requiremens
* PHP ≥ 5.6
* MySQL ≥ 5.6

## Attributions
* [running-coder/jquery-typeahead](https://github.com/running-coder/jquery-typeahead)
* [bgrins/spectrum](https://github.com/bgrins/spectrum)
* [balohmatevz/DMI2PNG](https://github.com/balohmatevz/DMI2PNG)

## About
Importing [tgstation/tgstation](https://github.com/tgstation/tgstation) as a `submodule`, this tool will turn Byond's DMI graphics files into separate PNGs. These PNGs are then overlaid on top of each other to form a complete mob sprite, which can then be used elsewhere.

**Caveat:** Generated image files are NOT saved on the server. You _must_ download and re-host them elsewhere yourself.

## Authentication
Some tools are blocked behind an authentication check that ensures users are known administrators on the game server. The tools can authenticate people in one of three ways:

1. *Remote authority authentication*, which is heavily dependent upon external services being properly configured. You probably won't need to use this.

2. *Text based authentication*, which checks the publicly available `admins.txt` file published by your game server.

3. *Database authentication*, where users are checked against the game server database. If they are an admin or higher, and have connected to the game server once in the last 24 hours, they will be authenticated. Otherwise, they will be marked as a player with no special access. This will be the best option for most tool installations.

##WIP
This project is very much a work in progress. Expect things to not work.
