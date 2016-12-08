<?php

// ==================================================
// > POST /auth/signup
// --------------------------------------------------
//   Excepts a json body with the attributes "Email"
// and "Password".
//   Returns a json containing the jwt token under
// the attribute "token", that token must be resend
// to the server under the "Authorization" header.
// ==================================================
$app->post('/auth/signup', function ($request, $response) {
  // Get post's body
  $data = $request->getParsedBody();
  // Create a new user
  $user = new User();
  $user->setEmail($data["Email"]);
  $user->setPassword(password_hash($data["Password"], PASSWORD_DEFAULT));
  $user->save();
  return $response
    ->withStatus(200)
    ->withJson([
      "validated" => true,
      "token" => auth\createUserToken($user)
    ]);
});

// ==================================================
// > POST /auth/login
// --------------------------------------------------
//   Excepts a json body with the attributes "Email"
// and "Password".
//   If the credentials are rigths, returns a json
// containing the jwt token under the attribute
// "token", that token must be resend to the server
// under the "Authorization" header.
//   If not, returns and error message under the
// "msg" attribute.
// ==================================================
$app->post('/auth/login', function ($request, $response) {
  // Get post's body
  $data = $request->getParsedBody();
  // Retrieve the user
  $user = UserQuery::create()->findOneByEmail($data["Email"]);
  // Check if an user have been found and if the passwords match
  if ($user && password_verify($data["Password"], $user->getPassword())) {
    // Valid credentials
    return $response->withJson([
      "validated" => true,
      "token" => auth\createUserToken($user)
    ]);
  } else {
    // Invalid credentials
    return $response->withJson([
      "validated" => false,
      "msg" => "Wrong credentials"
    ]);
  }
});

// ==================================================
// > GET /auth/user
// --------------------------------------------------
//   Returns all infos concerning the logged user.
// ==================================================
$app->get('/auth/user', function ($request, $response) {
  // Retrieve user_id
  $user_id = auth\getUserId($request);
  // Look for the user
  $user = UserQuery::create()->findPK($user_id);
  if ($user) {
    return $response->withJson($user->toArray());
  } else {
    return $response->withStatus(404);
  }
})->add($mwCheckLogged);
