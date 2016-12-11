<?php

// ==================================================
// > GET /api/farms/{id}
// ==================================================
$app->get('/api/farms/{id}', function ($request, $response, $args) {
  return api\view($response, FarmQuery::create()->findPK($args['id']));
});

// ==================================================
// > GET /api/farms/
// ==================================================
$app->get('/api/farms/', function ($request, $response) {
  $farms = FarmQuery::create()->find();
  return api\mapCollection(
    $response, $farms,
    function ($farm) use ($request) {
      $data = $farm->serialize();
      if (auth\isUser($request)) {
        $data["subscribed"] = false; // TODO: $farm->getSubscribers()->toArray();
      }
      return $data;
    }
  );
});

// ==================================================
// > POST /api/farms/
// ==================================================
$app->post('/api/farms/', function ($request, $response) {
  try {
    $farm = new Farm();
    $farm->setOwnerId(auth\getUser($request)->getId());
    return api\update($request, $response, $farm);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsLogged');

// ==================================================
// > PUT /api/farms/
// ==================================================
$app->put('/api/farms/{id}', function ($request, $response, $args) {
  $farm = FarmQuery::create()->findPK($args['id']);
  if ($farm == null) return $response->withStatus(404);
  if ($farm->getOwnerId() != auth\getUser($request)->getId()) {
    return $response->withStatus(401);
  }
  try {
    return api\update($request, $response, $farm);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsFarmer');


// ==================================================
// > DELETE /api/farms/
// ==================================================
$app->delete('/api/farms/{id}', function ($request, $response, $args) {
  $farm = FarmQuery::create()->findPK($args['id']);
  if ($farm == null) return $response->withStatus(404);
  if ($farm->getOwnerId() != auth\getUser($request)->getId()) {
    return $response->withStatus(401);
  }
  try {
    $farm->delete();
    return $response->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsFarmer');
