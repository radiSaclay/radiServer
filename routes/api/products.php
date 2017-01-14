<?php

// ==================================================
// > GET /api/products/{id}
// Returns the product id
// ==================================================
$app->get('/api/products/{id}', function ($request, $response, $args) {
  $user = auth\isUser($request)
    ? auth\getUser($request)
    : false;
  return api\view(
    $request, $response,
    ProductQuery::create()->findPK($args['id']),
    function ($item) use ($user) {
      if ($user) return [ "subscribed" => $item->hasSubscriber($user) ];
    }
  );
});

// ==================================================
// > GET /api/products/
// Returns all the products
// ==================================================
$app->get('/api/products/', function ($request, $response) {
  $user = auth\isUser($request)
    ? auth\getUser($request)
    : false;
  return api\listCollection(
    $request, $response,
    ProductQuery::create(),
    function ($item) use ($user) {
      if ($user) return [ "subscribed" => $item->hasSubscriber($user) ];
    }
  );
});

// ==================================================
// > POST /api/products/ Create product
// ==================================================
$app->post('/api/products/', function ($request, $response) {
  try {
    $product = new Product();
    return api\update($request, $response, $product);
  } catch (Exception $e) {
    return $response->withStatus(400)
    ->withJson(["Error = " => " " . $e->getMessage()]);
  }
})->add('mwIsAdmin');

// ==================================================
// > PUT /api/products/ Update product
// ==================================================
$app->put('/api/products/{id}', function ($request, $response, $args) {
  $product = ProductQuery::create()->findPK($args['id']);
  if ($product == null) return $response->withStatus(404);
  try {
    return api\update($request, $response, $product);
  } catch (Exception $e) {
    return $response->withStatus(400)
    ->withJson(["Error = " => " " . $e->getMessage()]);
  }
})->add('mwIsAdmin');


// ==================================================
// > DELETE /api/products/{id}
// ==================================================
$app->delete('/api/products/{id}', function ($request, $response, $args) {
  $product = ProductQuery::create()->findPK($args['id']);
  if ($product == null) return $response->withStatus(404);
  try {
    $product->delete();
    return $response->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400)
    ->withJson(["Error = " => " " . $e->getMessage() .
     " Maybe the element to be deleted is a foreign key elsewhere."]);
  }
})->add('mwIsAdmin');
