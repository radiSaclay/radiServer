<?php

// ==================================================
// > GET /api/farms/{id}
// ==================================================
$app->get('/api/farms/{id}', function ($request, $response, $args) {
  $farm = FarmQuery::create()->findPK($args['id']);
  if ($farm) {
    return $response->withJson($farm->toArray(), 200);
  } else {
    return $response->withStatus(404);
  }
});

// ==================================================
// > GET /api/farms/
// ==================================================
$app->get('/api/farms/', function ($request, $response) {
  $farms = FarmQuery::create()->find()->toArray();
  return $response->withJson($farms, 200);
});

// ==================================================
// > POST /api/farms/
// ==================================================
$app->post('/api/farms/', function ($request, $response) {
  $farm = new Farm();
  $farm->fromArray($request->getParsedBody());
  try {
    $farm->save();
    return $response
      ->withJson($farm->toArray())
      ->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add($mwCheckLogged);

// ==================================================
// > PUT /api/farms/
// ==================================================
$app->put('/api/farms/{id}', function ($request, $response, $args) {
  $farm = FarmQuery::create()->findPK($args['id']);
  if ($farm) {
    if ($farms->getOwnerId() === auth\getUserId()) {
      try {
        $farm->update($request->getParsedBody());
        return $response
          ->withJson($farm->toArray())
          ->withStatus(200);
      } catch (Exception $e) {
        return $response->withStatus(400);
      }
    } else {
      // Not the owner of the farm
      return $response->withStatus(401);
    }
  } else {
    return $response->withStatus(404);
  }
})->add($mwCheckLogged);


// ==================================================
// > DELETE /api/farms/
// ==================================================
$app->delete('/api/farms/{id}', function ($request, $response, $args) {
  $farm = FarmQuery::create()->findPK($args['id']);
  if ($farm) {
    if ($farms->getOwnerId() === auth\getUserId()) {
      try {
        $farm->delete();
        return $response->withStatus(200);
      } catch (Exception $e) {
        return $response->withStatus(400);
      }
    } else {
      // Not the owner of the farm
      return $response->withStatus(401);
    }
  } else {
    return $response->withStatus(404);
  }
})->add($mwCheckLogged);
