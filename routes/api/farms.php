<?php

// ==================================================
// > GET /api/farms/{id}
// ==================================================
$app->get('/api/farms/{id}', function ($request, $response, $args) {
  $farm = FarmQuery::create()->findPK($args['id']);
  if ($farm) {
    return $response->withJson(api\serializefarm($farm, auth\getUser($request)), 200);
  } else {
    return $response->withStatus(404);
  }
});

// ==================================================
// > GET /api/farms/
// ==================================================
$app->get('/api/farms/', function ($request, $response) {
  $farms = [];
  foreach(FarmQuery::create()->find() as $farm) {
    $farms[] = api\serializefarm($farm, auth\getUser($request));
  }
  return $response->withJson($farms, 200);
});

// ==================================================
// > POST /api/farms/
// ==================================================
$app->post('/api/farms/', function ($request, $response) {
  $farm = new Farm();
  $farm->setOwnerId(auth\getToken($request)["user_id"]);
  try {
    api\unserializeFarm($farm, $request->getParsedBody());
    $farm->save();
    return $response->withJson(api\serializeFarm($farm), 200);
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
    if ($farm->getOwnerId() === auth\getUserId($request)) {
      try {
        api\unserializeFarm($farm, $request->getParsedBody());
        $farm->save();
        return $response->withJson(api\serializeFarm($farm), 200);
      } catch (Exception $e) {
        return $response->withStatus(403);
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

$app->post('/api/farms/subscribe/{id}', function ($request, $response, $args) {
  $farm = FarmQuery::create()->findPK($args['id']);
  $user = auth\getUser($request);
  if ($farm) {
    if (api\hasSubscribed($user, $args['id'], 'farm') == false) {
      $link = new Subscription();
      $link->setUserId($user->getId());
      $link->setSubscriptiontype('farm');
      $link->setSubscriptionId($args['id']);
      $link->save();
      return $response->withStatus(200);
    } else {
      return $response->withStatus(400);
    }
  } else {
    return $response->withStatus(404);
  }
})->add($mwCheckLogged);

$app->post('/api/farms/unsubscribe/{id}', function ($request, $response, $args) {
  SubscriptionQuery::create()
    ->filterByUser(auth\getUser($request))
    ->filterBySubscriptionId($args['id'])
    ->filterBySubscriptionType('farm')
    ->delete();
  return $response->withStatus(200);
})->add($mwCheckLogged);
