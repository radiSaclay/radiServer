# RadiServer
![build status](https://travis-ci.org/radiSaclay/radiServer.svg?branch=master)

> The server for radiSaclay's project. Exposes a REST API to be used by mobile apps.

## Installation
#### Dependencies
This server uses [Slim](https://www.slimframework.com) microframework (for routing, etc.) and [Propel](http://propelorm.org/) as its ORM (to interface the database). Authentication is done with [jwt tokens](https://github.com/firebase/php-jwt), the environement is loaded through [.env](https://github.com/vlucas/phpdotenv) file.
It requires PHP 5.5+ and a database that can be configured in `./propel/propel.json` (see propel's documentation for more info.).

#### How to install
- Clone that repository on your server
- Install the dependencies using [composer](https://getcomposer.org/) by running `composer install` in you root directory.
- Copy `./src/config.example.php` to `./src/config.php` and fill it with the proper values.
- Copy `./propel/propel.example.json` to `./propel/propel.json`.
- Create a database and reference it in `./propel/propel.json` (see propel's documentation for more info.).
- Run `composer propel:update` to migrate databases.
- Your server is now ready to go.

## Usage

#### Authentification
[See docs](docs/routes/auth.md)

#### REST API

You can access a REST API for `farms`, `products` and `events`.

##### Create `POST /api/XXXs/`

##### Read `GET /api/XXXs/`
This route will list all items. The result is paginated and you need to query with `offset` and `limit` arguments to get other pages.
You can also set the level of details of the returned content with the `details` and  `embedded` arguments.

##### Read `GET /api/XXXs/:id`
This route will show the item which id is `:id`.
You can also set the level of details of the returned content with the `details` and  `embedded` arguments.

##### Update `PUT /api/XXXs/:id`
##### Delete `DELETE /api/XXXs/:id`

For more informations see [the detailled doc](docs/routes/api/).

## Routes

[See docs](docs)

## File structure
```
.
├─ ...
├─ propel
|   ├─ ...
|   ├─ generated-classes (Propel classes)
|   ├─ propel.json
|   └─ schema.xml
├─ public
|   ├─ .htacess
|   └─ index.php
├─ routes
|   └─ (your routes here)
├─ src
|   ├─ ...
|   └─ config.php
├─ tests
|   ├─ bootstrap.php
|   └─ (your tests here)
├─ tools
|   └─ (your tools here)
├─ .htaccess
├─ composer.json
└─ README.md (you are here)
```
