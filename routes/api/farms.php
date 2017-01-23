<?php

// ==================================================
// > GET /api/farms/subscribed
// Returns the farms and also a flag "subscribed" when user is subscribed to a
// farm
// ==================================================
$app->get('/api/farms/subscribed', function ($request, $response) {
  $user = auth\getUser($request);
  return api\listCollection(
    $request, $response,
    FarmQuery::create()->filterBySubscriber($user)
  );
})->add('mwIsLogged');

// ==================================================
// > GET /api/farms/{id}
// Just returns the given farm
// ==================================================
$app->get('/api/farms/{id}', function ($request, $response, $args) {
  return api\view(
    $request, $response,
    FarmQuery::create()->findPK($args['id'])
  );
});

// ==================================================
// > GET /api/farms/
// Returns the farms and also a flag "subscribed" when user is subscribed to a
// farm
// ==================================================
$app->get('/api/farms/', function ($request, $response) {
  return api\listCollection(
    $request, $response,
    FarmQuery::create()
  );
});

// ==================================================
// > POST /api/farms/
// ==================================================
$app->post('/api/farms/', function ($request, $response) {
  $farm = new Farm();
  $farm->setOwnerId(auth\getUser($request)->getId());
  return api\update(
    $request, $response,
    $farm
  );
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
  return api\update(
    $request, $response,
    $farm
  );
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
  return api\delete(
    $request, $response,
    $farm
  );
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
