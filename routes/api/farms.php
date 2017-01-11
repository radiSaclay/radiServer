<?php

// ==================================================
// > GET /api/farms/{id}
// Just returns the given farm
// ==================================================
$app->get('/api/farms/{id}', function ($request, $response, $args) {
  return api\view($response, FarmQuery::create()->findPK($args['id']));
});

// ==================================================
// > GET /api/farms/
// Returns the farms and also a flag "subscribed" when user is subscribed to a
// farm
// ==================================================
$app->get('/api/farms/', function ($request, $response) {
  $user  = auth\isUser($request)
    ? auth\getUser($request)
    : false;
  return api\listCollection(
    $request, $response,
    FarmQuery::create(),
    function ($item) use ($user) {
      if ($user) {
        return [ "subscribed" => $item->hasSubscriber($user) ];
      } else {
        return [];
      }
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

// ==================================================
// > POST /api/farms/subscribe/{id}
// ==================================================
$app->post('/api/farms/subscribe/{id}', function ($request, $response, $args) {
  $user = auth\getUser($request);
  $farm = FarmQuery::create()->findPK($args['id']);
  if ($farm == null) return $response->withStatus(404);
  try {
    $farm->addSubscriber($user);
    $farm->save();
    return $response->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsLogged');

// ==================================================
// > POST /api/farms/unsubscribe/{id}
// ==================================================
$app->post('/api/farms/unsubscribe/{id}', function ($request, $response, $args) {
  $user = auth\getUser($request);
  $farm = FarmQuery::create()->findPK($args['id']);
  if ($farm == null) return $response->withStatus(404);
  try {
    $farm->removeSubscriber($user);
    $farm->save();
    return $response->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsLogged');
