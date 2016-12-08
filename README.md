# RadiServer

> The server for radiSaclay's project. Exposes a REST API to be used by mobile apps.

## Installation
#### Dependencies
This server uses [Slim](https://www.slimframework.com) microframework (for routing, etc.) and [Propel](http://propelorm.org/) as its ORM (to interface the database). Authentication is done with [jwt tokens](https://github.com/firebase/php-jwt), the environement is loaded through [.env](https://github.com/vlucas/phpdotenv) file.
It requires PHP 5.5+ and a database that can be configured in `./propel/propel.json` (see propel's documentation for more info.).

#### How to install
- Clone that repository on your server
- Install the dependencies using [composer](https://getcomposer.org/) by running `composer install` in you root directory.
- Copy `./env.example` to `./.env` and fill it with the proper values.
- Copy `./propel/propel.example.json` to `./propel/propel.json.
- Create a database and reference it in `./propel/propel.json` (see propel's documentation for more info.), then create the propel configuration file with `propel config:convert` in the `./propel` directory.
- Migrate the database with propel command lines, `propel sql:build` and `propel sql:insert`.
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

To log in or sign in, you must sent a post request to the proper route and a json body such as:
```json
{
  "Email": "victor.hugo@paris.fr",
  "Password": "Causette"
}
```

You will recieve a response containing a json looking like :
```json
{
  "validated": true,
  "token": "AAA.BBB.CCC"
}
```
You must resend that token with every request under the header `Authorization` so as to be recognizedd by the server.

- GET `auth/user`
Will send you back all info about the user corresponding to the token you have sent.

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
|   ├─ auth.php
|   ├─ jwt.php
|   └─ middleware.php
├─ .env
├─ .htaccess
├─ .composer
└─ README.md (you are here)
```
