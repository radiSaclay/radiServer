<?php

// ==================================================
// > GET /api/products/{id}
// Returns the product id
// ==================================================
$app->get('/api/products/{id}', function ($request, $response, $args) {
  return api\view($response, ProductQuery::create()->findPK($args['id']));
})->add('mwIsLogged');

// ==================================================
// > GET /api/products/
// Returns all the products
// ==================================================
$app->get('/api/products/', function ($request, $response) {
  $products = ProductQuery::create()->find();
  return api\mapCollection($response, $products, function ($products){
    $data = $products->serialize();
    return $data;
  });
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
