<?php

require_once '../vendor/autoload.php';
require_once '../propel/generated-conf/config.php';

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\App;

$app = new App();

// > API
require_once '../src/api.php';

// > API routes
$app->any('/api/farms/[{id}]', api\resource('farms'));
$app->any('/api/events/[{id}]', api\resource('events'));
$app->any('/api/products/[{id}]', api\resource('products'));

// > Wildcard
$app->get('[/{params:.*}]', function ($request, $response, $args) {
  var_dump(explode('/', $request->getAttribute('params')));
});

$app->run();
