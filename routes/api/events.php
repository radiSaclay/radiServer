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
    $events = EventQuery::create()
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
$app->put('/api/events/', function ($request, $response) {
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

// ==================================================
// > POST /api/events/
// ==================================================
$app->post('/api/events/{id}', function ($request, $response, $args) {
  $event = EventQuery::create()->findPK($args['id']);
  if ($event) {
    try {
      $event->update($request->getParsedBody());
      return $response
        ->withJson($event->toArray())
        ->withStatus(200);
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
