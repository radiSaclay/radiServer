<?php

// This is like the "main" file in C, C++ etc..

// Load dependecies
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../propel/generated-conf/config.php';

// Use Slim classes
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\App;

// Create the Slim App
$app = new App();

// CORS Cross-origin resource sharing
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
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/collection.php';
require_once __DIR__ . '/../src/jwt.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/api.php';
require_once __DIR__ . '/../src/middleware.php';

// > load routes from "./routes"
require __DIR__ . '/../routes/auth.php';
require __DIR__ . '/../routes/api/farms.php';
require __DIR__ . '/../routes/api/events.php';
require __DIR__ . '/../routes/api/products.php';

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
    if ($error->getMessage() == 'Expired token') {
      return $container['response']->withJson([ 'message' => $error->getMessage() ], 401);
    } else {
      return $container['response']->withJson([ 'message' => $error->getMessage() ], 500);
    }
  };
};

// Start the Slim App
$app->run();
