<?php

// ==================================================
// > GET /api/events/{id}
// ==================================================
$app->get('/api/events/{id}', function ($request, $response, $args) {
  $event = EventQuery::create()->findPK($args['id']);
  $user = auth\getUser($request);
  $user = auth\getUser($request);
  if ($event) {
    return $response->withJson(api\serializeEvent($event, $user), 200);
  } else {
    return $response->withStatus(404);
  }
});

// ==================================================
// > GET /api/events/
// ==================================================
$app->get('/api/events/', function ($request, $response) {
  // if (auth\isLogged($request)) {
  //   // An user is logged, you should send back a personalized stream
  //   // TODO
  // } else {
  //   // No user is logged, you should send back a generic stream
  //   $events = EventQuery::create()
  //     ->orderByCreatedAt('desc')
  //     ->limit(25)
  //     ->find()
  //     ->toArray();
  //   return $response->withJson($events, 200);
  // }
  $data = [];
  $user = auth\getUser($request);
  foreach(EventQuery::create()->orderByCreatedAt('desc')->find() as $event) {
    $data[] = api\serializeEvent($event, $user);
  }
  return $response->withJson($data, 200);
});

// ==================================================
// > POST /api/events/
// ==================================================
$app->post('/api/events/', function ($request, $response) {
  $event = new Event();
  $event->setFarmId(auth\getToken($request)["farm_id"]);
  try {
    api\unserializeEvent($event, $request->getParsedBody());
    $event->save();
    return $response->withJson(api\serializeEvent($event), 200);
  } catch (Exception $e) {
    return $response->withStatus(400);
  }
})->add($mwCheckLogged);

// ==================================================
// > PUT /api/events/
// ==================================================
$app->put('/api/events/{id}', function ($request, $response, $args) {
  $event = EventQuery::create()->findPK($args['id']);
  if ($event) {
    try {
      api\unserializeEvent($event, $request->getParsedBody());
      $event->save();
      return $response->withJson(api\serializeEvent($event), 200);
    } catch (Exception $e) {
      return $response->withStatus(400);
    }
  } else {
    return $response->withStatus(404);
  }
})->add($mwCheckLogged);


// ==================================================
// > DELETE /api/events/
// ==================================================
$app->delete('/api/events/{id}', function ($request, $response, $args) {
  $event = EventQuery::create()->findPK($args['id']);
  if ($event) {
    try {
      $event->delete();
      return $response->withStatus(200);
    } catch (Exception $e) {
      return $response->withStatus(400);
    }
  } else {
    return $response->withStatus(404);
  }
})->add($mwCheckLogged);

$app->post('/api/events/pin/{id}', function ($request, $response, $args) {
  $event = EventQuery::create()->findPK($args['id']);
  $user = auth\getUser($request);
  if ($event) {
    if (api\hasPinned($user, $event) == false) {
      $event->addUser($user);
      $event->save();
      return $response->withStatus(200);
    } else {
      return $response->withStatus(400);
    }
  } else {
    return $response->withStatus(404);
  }
})->add($mwCheckLogged);

$app->post('/api/events/unpin/{id}', function ($request, $response, $args) {
  $event = EventQuery::create()->findPK($args['id']);
  $user = auth\getUser($request);
  if ($event) {
    if (api\hasPinned($user, $event) == true) {
      $event->removeUser($user);
      $event->save();
      return $response->withStatus(200);
    } else {
      return $response->withStatus(400);
    }
  } else {
    return $response->withStatus(404);
  }
})->add($mwCheckLogged);
