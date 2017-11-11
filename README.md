WP-CLI Salt Command
===================

[![Package license](https://img.shields.io/packagist/l/salaros/wp-cli-salts-command.svg)](https://packagist.org/packages/salaros/wp-cli-salts-command)
[![Packagist type](https://img.shields.io/badge/Packagist-wp--cli--package-pink.svg)](https://packagist.org/packages/salaros/wp-cli-salts-command)
[![Packagist downloads](https://img.shields.io/packagist/dt/salaros/wp-cli-salts-command.svg)](https://packagist.org/packages/salaros/wp-cli-salts-command)
[![Monthly Downloads](https://poser.pugx.org/salaros/wp-cli-salts-command/d/monthly)](https://packagist.org/packages/salaros/wp-cli-salts-command)
[![Latest Stable Version](https://img.shields.io/packagist/v/salaros/wp-cli-salts-command.svg)](https://packagist.org/packages/salaros/wp-cli-salts-command)
[![composer.lock](https://poser.pugx.org/salaros/wp-cli-salts-command/composerlock)](https://packagist.org/packages/salaros/wp-cli-salts-command)

Manage salts for your WordPress installation

## Output salts to STDOUT

```
wp salts generate
```

This will grab new salts from the [WordPress Salt API](https://api.wordpress.org/secret-key/1.1/salt/) and output it to the `STDOUT`

## Output salts to a file

```
wp salts generate --file=/absolute/path/to/file.php
```

This will output the salts to a file. Because the file contains the complete `define()` code the salts will be set by a simple `require` somewhere in your wp-config.php

## Output salts as env vars

```
wp salts generate --format=env --file=/absolute/path/to/.env
```

This will output the salts as shell environment variables. Useful for projects that load configurations from .env files.

## Output salts as Yaml config file

```
wp salts generate --format=yaml --file=/absolute/path/to/file.yaml
```

This will output the salts as Yaml config file.
