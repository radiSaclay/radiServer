# RadiServer

> The server for radiSaclay's project. Exposes a REST API to be used by mobile apps.

## Installation
#### Dependencies
This server uses [Slim](https://www.slimframework.com) microframework (for routing, etc.) and [Propel](http://propelorm.org/) as its ORM (to interface the database).
It requires PHP 5.5+ and a database that can be configured in `./propel/propel.json` (see propel's documentation for more info.).

#### How to install
- Clone that repository on your server
- Install the dependencies using [composer](https://getcomposer.org/) by running `composer install` in you root directory.
- Copy `./env.example` to `./.env` and fill it with the proper values.
- Make sure you have created the database and properly referenced it in `./propel/propel.json` (see propel's documentation for more info.). You can migrate using propel command lines, `propel sql:build` and `propel sql:insert`. After you set the config for the database, do a `propel config:convert` to aplly the change.
- Your server is now ready to go.

## Routes
#### API
For `farms`, `products` and `events`, you have access to a complete REST CRUD API, routes are :
- GET `/api/xxx/` to list all items
- GET `/api/xxx/:id` to get one item by id
- POST `/api/xxx/` to create new one
- PUT `/api/xxx/:id` to update one item by id
- DELETE `/api/xxx/:id` to delete one item by id

#### Authentication
- POST `auth/signin`
- POST `auth/login`
- GET `auth/logout`
