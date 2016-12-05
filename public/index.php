<?php

require_once '../vendor/autoload.php';
require_once '../propel/generated-conf/config.php';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\App;

// > Env
$dotenv = new Dotenv\Dotenv(realpath('..'));
$dotenv->load();
// ---

$app = new App();

// > Load dependencies
require_once '../src/auth.php';
require_once '../src/api.php';

// =====================================================
//   ROUTES
// =====================================================

// > API
//  ~ GET    /api/XXXs/ -> list all
//  ~ GET    /api/XXXs/:id -> get one by id
//  ~ POST   /api/XXXs/ -> create new one
//  ~ PUT    /api/XXXs/:id -> update by id
//  ~ DELETE /api/XXXs/:id -> delete by id
$app->any('/api/farms/[{id}]', api\resource('farms'));
$app->any('/api/events/[{id}]', api\resource('events'));
$app->any('/api/products/[{id}]', api\resource('products'));

// > Users & Authentication
$app->post('/auth/signin', function ($req, $res) { return auth\signin($req, $res); });
$app->post('/auth/login', function ($req, $res) { return auth\login($req, $res); });
$app->get('/auth/logout', function ($req, $res) { return auth\logout($req, $res); });

// > Wildcard
$app->get('[/{params:.*}]', function ($request, $response, $args) {
  var_dump(explode('/', $request->getAttribute('params')));
});

$app->run();
