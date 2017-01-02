# RadiServer

> The server for radiSaclay's project. Exposes a REST API to be used by mobile apps.

## Installation
#### Dependencies
This server uses [Slim](https://www.slimframework.com) microframework (for routing, etc.) and [Propel](http://propelorm.org/) as its ORM (to interface the database). Authentication is done with [jwt tokens](https://github.com/firebase/php-jwt), the environement is loaded through [.env](https://github.com/vlucas/phpdotenv) file.
It requires PHP 5.5+ and a database that can be configured in `./propel/propel.json` (see propel's documentation for more info.).

#### How to install
- Clone that repository on your server
- Install the dependencies using [composer](https://getcomposer.org/) by running `composer install` in you root directory.
- Copy `./src/config.example.php` to `./src/config.php` and fill it with the proper values.
- Copy `./propel/propel.example.json` to `./propel/propel.json.
- Create a database and reference it in `./propel/propel.json` (see propel's documentation for more info.), then create the propel configuration file with `propel config:convert` in the `./propel` directory.
- Migrate the database with propel command lines, `propel sql:build` and `propel sql:insert`.
- Your server is now ready to go.

## Routes

[See docs](docs)

## File structure
```
.
├─ ...
├─ propel
|   ├─ ...
|   ├─ propel.json
|   └─ schema.xml
├─ public
|   ├─ .htacess
|   └─ index.php
├─ routes
|   └─ (your routes here)
├─ src
|   ├─ api.php
|   ├─ auth.php
|   ├─ config.php
|   ├─ jwt.php
|   └─ middleware.php
├─ .htaccess
├─ composer.json
└─ README.md (you are here)
```
