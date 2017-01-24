<?php

// Load dependecies
require_once '../vendor/autoload.php';
require_once '../propel/generated-conf/config.php';

// Use Slim classes
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\App;

// Create the Slim App
$app = new App();

// CORS
$app->add(function($request, $response, $next) {
  $response = $next($request, $response);
  return $response
    ->withHeader('Access-Control-Allow-Origin', "*")
    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
$app->options('/{routes:.+}', function ($request, $response, $args) {
  return $response
    ->withHeader('Access-Control-Allow-Origin', "*")
    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// > Load "./src" modules
// All kind of useful functions thematically sorted
require_once '../src/config.php';
require_once '../src/collection.php';
require_once '../src/jwt.php';
require_once '../src/auth.php';
require_once '../src/api.php';
require_once '../src/middleware.php';

// > load routes from "./routes"
require_once '../routes/auth.php';
require_once '../routes/api/farms.php';
require_once '../routes/api/events.php';
require_once '../routes/api/products.php';

// > Wildcard
// Will describe the path of all unregistered route.
// For debug purpose only
$app->get('[/{params:.*}]', function ($request, $response, $args) {
  var_dump(explode('/', $request->getAttribute('params')));
});

// Error handler
$container = $app->getContainer();
$container['errorHandler'] = function ($container) {
  return function ($request, $response, $error) use ($container) {
    // Format of exception to return
    return $container['response']->withJson([
      'message' => $error->getMessage()
    ], 500);
  };
};

// Start the Slim App
$app->run();
