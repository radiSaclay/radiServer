<?php

// Gets a single prodcut using its primary key
$app->get('/api/products/{primarykey}', function ($request, $response, $args) {
    $prods = new ProductQuery();
    $prods_arr = $prods->findPK($args['primarykey']);
    if($prods_arr === null){
      return $response->withStatus(404);
    }
    $prods_arr = $prods_arr->toArray();
    return $response->withJson($prods_arr, 201);
  }
);

// Returns all products
$app->get('/api/products/', function ($request, $response, $args) {
    $prods = ProductQuery::create()->find()->toArray();
    return $response->withJson($prods, 201);
  }
);

// Creates new product
// The new product name is received in $request which should be a json file
// having a key 'name' with value corresponding to the name of the new product
$app->put('/api/products/', function ($request, $response, $args) {
    $new_prod = new Product();
    $parsedBody = $request->getParsedBody();
    if($parsedBody['name'] === null){
      return $response->withStatus(400);
    }
    $new_prod->setName($parsedBody['name']);
    $new_prod->save();
  }
);

// Updates the product
// The product to be updated is the one with id = primarykey and its new name
// is passed inside the $request json file (value of the ket 'new_name')
$app->post('/api/products/{primarykey}', function ($request, $response, $args) {
    $parsedBody = $request->getParsedBody();
    if(!array_key_exists('new_name', $parsedBody) || $args['primarykey'] === null){
      return $response->withStatus(400);
    }
    $new_prod = ProductQuery::create()->findPK($args['primarykey']);
    if($new_prod === null){
      return $response->withStatus(404);
    }
    $new_prod->setName($parsedBody['new_name']);
    $new_prod->save();
  }
);

// Deletes the product with id primarykey
$app->delete('/api/products/{primarykey}', function ($request, $response, $args) {
    if($args['primarykey'] === null){
      return $response->withStatus(404);
    }
    $new_prod = ProductQuery::create()->findPK($args['primarykey']);
    if($new_prod === null){
      return $response->withStatus(404);
    }
    $new_prod->delete();
  }
);
