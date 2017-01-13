<?php

// ==================================================
// > GET /api/events/{id}
// Returns the event id
// ==================================================
$app->get('/api/events/{id}', function ($request, $response, $args) {
  $event = EventQuery::create()->findPK($args['id']);
  return \api\view($response, return_event($event, $request));

});

// ==================================================
// > GET /api/events/
// If it is a farmer requesting returns all events of its farms and add the number
// of users who have pinned the event.
// If it is an user return all events and a bool pinned corresponding to the user
// having pinned or not the event
// ==================================================
$app->get('/api/events/', function ($request, $response) {
  // If the request comes from a farmer, $events = auth\getFarm($request)->getEvents() (Farm events)
  // Else EventQuery::create()->find() (All events)
  $events = auth\isFarmer($request)
    ? auth\getFarm($request)->getEvents()
    : EventQuery::create()->find();

  return api\mapCollection(
    $response, $events,
    function ($event) use ($request) {
      return return_event($event, $request);
    }
  );
});

function return_event($event, $request) {
  $data = $event->serialize();
  // if it is farmer requesting add number of users to each event
  if (auth\isFarmer($request)) {
    $data["pins"] = $event->countUsers();
  } else if (auth\isUser($request)) {
    // if it is user flag the events it pinned
    $user = auth\getUser($request);
    $data["pinned"] = $event->getUsers()->contains($user);
  }
  return $data;
}

// ==================================================
// > POST /api/events/ Create event
// Receives an event and checks if it is from a farmer,
// marks the farm who sent it as the owner of the event
// and puts it in the DB
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
// > PUT /api/events/ Update event
// Receives an event and checks if it comes from a farmer
// If event exists it updates it in DB
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
// Adds user to event (pins it)
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
