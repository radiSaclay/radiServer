<?php

// ==================================================
// > GET /api/events/{id}
// ==================================================
$app->get('/api/events/{id}', function ($request, $response, $args) {
  return api\view($response, EventQuery::create()->findPK($args['id']));
});

// ==================================================
// > GET /api/events/
// ==================================================
$app->get('/api/events/', function ($request, $response) {
  $events = auth\isFarmer($request)
    ? auth\getFarm($request)->getEvents()
    : EventQuery::create()->find();
  return api\mapCollection(
    $response, $events,
    function ($event) use ($request) {
      $data = $event->serialize();
      if (auth\isFarmer($request)) {
        $data["pins"] = $event->countUsers();
      } else if (auth\isUser($request)) {
        $user = auth\getUser($request);
        $data["pinned"] = $event->getUsers()->contains($user);
      }
      return $data;
    }
  );
});

// ==================================================
// > POST /api/events/
// ==================================================
$app->post('/api/events/', function ($request, $response) {
  try {
    $event = new Event();
    $event->setFarmId(auth\getFarm($request)->getId());
    return api\update($request, $response, $event);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsFarmer');

// ==================================================
// > PUT /api/events/
// ==================================================
$app->put('/api/events/{id}', function ($request, $response, $args) {
  $event = EventQuery::create()->findPK($args['id']);
  if ($event == null) return $response->withStatus(404);
  if ($event->getFarmId() != auth\getFarm($request)->getId()) {
    return $response->withStatus(401);
  }
  try {
    return api\update($request, $response, $event);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsFarmer');


// ==================================================
// > DELETE /api/events/
// ==================================================
$app->delete('/api/events/{id}', function ($request, $response, $args) {
  $event = EventQuery::create()->findPK($args['id']);
  if ($event == null) return $response->withStatus(404);
  if ($event->getFarmId() != auth\getFarm($request)->getId()) {
    return $response->withStatus(401);
  }
  try {
    $event->delete();
    return $response->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsFarmer');

// ==================================================
// > POST /api/events/pin/{id}
// ==================================================
$app->post('/api/events/pin/{id}', function ($request, $response, $args) {
  $user = auth\getUser($request);
  $event = EventQuery::create()->findPK($args['id']);
  if ($event == null) return $response->withStatus(404);
  try {
    $event->addUser($user);
    $event->save();
    return $response->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsLogged');

// ==================================================
// > POST /api/events/unpin/{id}
// ==================================================
$app->post('/api/events/unpin/{id}', function ($request, $response, $args) {
  $user = auth\getUser($request);
  $event = EventQuery::create()->findPK($args['id']);
  if ($event == null) return $response->withStatus(404);
  try {
    $event->removeUser($user);
    $event->save();
    return $response->withStatus(200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add('mwIsLogged');
