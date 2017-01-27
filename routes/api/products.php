<?php

// ==================================================
// > GET /api/farms/subscribed
// Returns the farms and also a flag "subscribed" when user is subscribed to a
// farm
// ==================================================
$app->get('/api/products/subscribed', function ($request, $response) {
  $user = auth\getUser($request);
  return api\listCollection(
    $request, $response,
    ProductQuery::create()->filterBySubscriber($user)
  );
})->add('mwIsLogged');

// ==================================================
// > GET /api/products/{id}
// Returns the product id
// ==================================================
$app->get('/api/products/{id}', function ($request, $response, $args) {
  return api\view(
    $request, $response,
    ProductQuery::create()->findPK($args['id'])
  );
});

// ==================================================
// > GET /api/products/
// Returns all the products
// ==================================================
$app->get('/api/products/', function ($request, $response) {
  return api\listCollection(
    $request, $response,
    ProductQuery::create()
  );
});

// ==================================================
// > POST /api/products/ Create product
// ==================================================
$app->post('/api/products/', function ($request, $response) {
  return api\update(
    $request, $response,
    new Product()
  );
});

// ==================================================
// > PUT /api/products/ Update product
// ==================================================
$app->put('/api/products/{id}', function ($request, $response, $args) {
  $product = ProductQuery::create()->findPK($args['id']);
  if ($product == null) return $response->withStatus(404);
  return api\update(
    $request, $response,
    $product
  );
})->add('mwIsAdmin');


// ==================================================
// > DELETE /api/products/{id}
// ==================================================
$app->delete('/api/products/{id}', function ($request, $response, $args) {
  $product = ProductQuery::create()->findPK($args['id']);
  if ($product == null) return $response->withStatus(404);
  return api\delete(
    $request, $response,
    $product
  );
})->add('mwIsAdmin');

// ==================================================
// > POST /api/products/subscribe/{id}
// ==================================================
$app->post('/api/products/subscribe/{id}', function ($request, $response, $args) {
  $user = auth\getUser($request);
  $product = ProductQuery::create()->findPK($args['id']);
  if ($product == null) return $response->withStatus(404);
  try {
    $product->addSubscriber($user);
    $product->save();
    return $response->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsLogged');

// ==================================================
// > POST /api/products/unsubscribe/{id}
// ==================================================
$app->post('/api/products/unsubscribe/{id}', function ($request, $response, $args) {
  $user = auth\getUser($request);
  $product = ProductQuery::create()->findPK($args['id']);
  if ($product == null) return $response->withStatus(404);
  try {
    $product->removeSubscriber($user);
    $product->save();
    return $response->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsLogged');
