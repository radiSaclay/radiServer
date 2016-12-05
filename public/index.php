<?php

require_once '../vendor/autoload.php';
require_once '../propel/generated-conf/config.php';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\App;

// > Env
// Load "./.env" file as the "$_ENV" variable
$dotenv = new Dotenv\Dotenv(realpath('..'));
$dotenv->load();
// ---

// Create the Slim App
$app = new App();

// > Load "./src" modules
// All kind of usefull functions thematically sorted
require_once '../src/auth.php';
require_once '../src/api.php';

// =====================================================
//   ROUTES
// =====================================================

// > Middleware
$checkLogged = function ($req, $res, $next) {
  $token = \auth\getToken($req);
  if ($token && $token["user_id"]) {
    return $next($req, $res);
  } else {
    return $res->withStatus(401);
  }
};

// > API
//  ~ GET    /api/XXXs/ -> list all
//  ~ GET    /api/XXXs/:id -> get one by id
//  ~ POST   /api/XXXs/ -> create new one
//  ~ PUT    /api/XXXs/:id -> update by id
//  ~ DELETE /api/XXXs/:id -> delete by id
$app->any('/api/farms/[{id}]', api\resource('farms'))->add($checkLogged);
$app->any('/api/events/[{id}]', api\resource('events'));
$app->any('/api/products/[{id}]', api\resource('products'));

// > Users & Authentication
$app->post('/auth/signin', function ($req, $res) { return auth\signin($req, $res); });
$app->post('/auth/login', function ($req, $res) { return auth\login($req, $res); });

// > Wildcard
$app->get('[/{params:.*}]', function ($request, $response, $args) {
  var_dump(explode('/', $request->getAttribute('params')));
});

// Start the Slim App
$app->run();
