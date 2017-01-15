<?php

// ==================================================
// > GET /api/products/{id}
// Returns the product id
// ==================================================
$app->get('/api/products/init', function ($request, $response, $args) {
  if (ProductQuery::create()->count() == 0) {
    $all = new Product();
    $all->setName("Tout");
    $all->makeRoot();
    $all->save();
    return api\view($request, $response, $all);
  } else {
    var_dump(ProductQuery::create()->count());
  }
})->add('mwIsAdmin');

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
})->add('mwIsAdmin');

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
