<?php

// ==================================================
// > POST /auth/login
// ==================================================
$app->post('/auth/login', function ($request, $response) {
  return auth\login($request, $response);
});

// ==================================================
// > POST /auth/signup
// ==================================================
$app->post('/auth/signup', function ($request, $response) {
  $data = $request->getParsedBody();
  try {
    $user = new User();
    $user->setEmail($data["email"]);
    $user->setPassword(password_hash($data["password"], PASSWORD_DEFAULT));
    $user->save();
  } catch (Exception $e) {
    return $response->withJson([ "validated" => false, "msg" => "Could not sign up" ]);
  }
  return auth\login($request, $response);
});

// ==================================================
// > DELETE /auth/delete
// ==================================================
$app->delete('/auth/delete', function ($request, $response) {
  $user = auth\getUser($request);
  if ($user) {
    try {
      $user->delete();
      return $response->withStatus(200);
    } catch(Exception $e) {
      return $response->withJson("Exception: " . $e->getMessage(), 500);
    }
  } else {
    return $response->withStatus(404);
  }
});


// ==================================================
// > GET /auth/user
// ==================================================
$app->get('/auth/user', function ($request, $response) {
  $user = auth\getUser($request);
  if ($user) {
    $data = $user->serialize();
    $farm = auth\getUserFarm($user);
    if ($farm) $data["farm"] = $farm->serialize();
    return $response->withJson($data, 200);
  } else {
    return $response->withStatus(404);
  }
})->add('mwIsLogged');
