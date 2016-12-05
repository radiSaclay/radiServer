<?php

// ==================================================
// > POST /auth/signin
// --------------------------------------------------
//   Excepts a json body with the attributes "Email"
// and "Password".
//   Returns a json containing the jwt token under
// the attribute "token", that token must be resend
// to the server under the "Authorization" header.
// ==================================================
$app->post('/auth/signin', function ($request, $response) {
  $user = new User();
  $user->fromArray($request->getParsedBody());
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
  if ($user && $user->getPassword() === $data["Password"]) {
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
