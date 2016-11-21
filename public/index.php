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
$app->any('/api/books/[{id}]', api\resource('books'));
$app->any('/api/authors/[{id}]', api\resource('authors'));
$app->any('/api/publishers/[{id}]', api\resource('publishers'));

// > Wildcard
$app->get('[/{params:.*}]', function ($request, $response, $args) {
  var_dump(explode('/', $request->getAttribute('params')));
});

$app->run();
