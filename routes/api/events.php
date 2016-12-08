<?php

// ==================================================
// > GET /api/events/{id}
// ==================================================
$app->get('/api/events/{id}', function ($request, $response, $args) {
  $event = EventQuery::create()->findPK($args['id']);
  if ($event) {
    return $response->withJson($event->toArray(), 201);
  } else {
    return $response->withStatus(404);
  }
});

// ==================================================
// > GET /api/events/
// ==================================================
$app->get('/api/events/', function ($request, $response) {
  if (auth\isLogged($request)) {
    // An user is logged, you should send back a personalized stream
    // TODO
  } else {
    // No user is logged, you should send back a generic stream
    $events = ProductQuery::create()
      ->orderByCreatedAt('desc')
      ->limit(25)
      ->find()
      ->toArray();
    return $response->withJson($events, 201);
  }
});


// ==================================================
// > PUT /api/events/
// ==================================================
$app->put('/api/events/', function ($request, $response, $args) {
  $event = new Event();
  $event->fromArray($request->getParsedBody());
  try {
    $event->save();
    return $response
      ->withJson($event->toArray())
      ->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add($mwCheckLogged);

// // Updates the product
// // The product to be updated is the one with id = primarykey and its new name
// // is passed inside the $request json file (value of the ket 'new_name')
// $app->post('/api/products/{primarykey}', function ($request, $response, $args) {
//     $parsedBody = $request->getParsedBody();
//     if(!array_key_exists('new_name', $parsedBody) || $args['primarykey'] === null){
//       return $response->withStatus(400);
//     }
//     $new_prod = ProductQuery::create()->findPK($args['primarykey']);
//     if($new_prod === null){
//       return $response->withStatus(404);
//     }
//     $new_prod->setName($parsedBody['new_name']);
//     $new_prod->save();
//   }
// );
//
// // Deletes the product with id primarykey
// $app->delete('/api/products/{primarykey}', function ($request, $response, $args) {
//     if($args['primarykey'] === null){
//       return $response->withStatus(404);
//     }
//     $new_prod = ProductQuery::create()->findPK($args['primarykey']);
//     if($new_prod === null){
//       return $response->withStatus(404);
//     }
//     $new_prod->delete();
//   }
// );
