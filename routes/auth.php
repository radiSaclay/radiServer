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
    return $response->withJson([ "validated" => false, "msg" => "Could not sign up" ], 400);
  }
  return auth\login($request, $response);
});


// ==================================================
// > POST /auth/fb
// Login or account creation using Facebook API
// Expects a header with a facebook access token
// Returns a jwt token which can be used to access the server api
// ==================================================
$app->post('/auth/fb', function ($request, $response) {
  $fb = new Facebook\Facebook([
    'app_id' => '259526644492644',
    'app_secret' => 'e1b06f3978feea4eab73dca305397b4a',
    'default_graph_version' => 'v2.8'
  ]);
  $fb->setDefaultAccessToken(\jwt\getAuthJWT($request));
  try {
    $fb_response = $fb->get('/me');
    $userNode = $fb_response->getGraphUser();
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    return $response->withJson('Graph returned an error: ' . $e->getMessage(), 500);
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    return $response->withJson('Facebook SDK returned an error: ' . $e->getMessage(), 500);
  }
  $user_id = $userNode->getId();
  $user = UserQuery::create()->findOneByFbId($user_id);
  if($user){
    return $response->withJson([ "validated" => true, "token" => \auth\createUserToken($user) ]);
  }else{
    $user = new User();
    $user->setFbId($user_id);
    $user->save();
    return $response->withJson([ "validated" => true, "token" => \auth\createUserToken($user) ]);
  }
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
