<?php namespace auth;

require_once 'jwt.php';

// Create a token for a specific user
function createUserToken (\User $user) {
  return createToken([
    "user_id" => $user->getId()
  ], 60 * 60 * 24 * 7);
}

// Create the new user and save it
function signin ($req, $res) {
  $user = new \User();
  $user->fromArray($req->getParsedBody());
  $user->save();
  return $res
    ->withStatus(200)
    ->withJson([
      "validated" => true,
      "token" => createUserToken($user)
    ]);
}

// Verify the user credentials, create a token and send it back
function login ($req, $res) {
  $data = $req->getParsedBody();
  $user = \UserQuery::create()->findOneByEmail($data["Email"]);
  if ($user && $user->getPassword() === $data["Password"]) {
    return $res->withJson([
      "validated" => true,
      "token" => createUserToken($user)
    ]);
  }
  return $res->withJson([
    "validated" => false,
    "msg" => "Wrong credentials"
  ]);
}

// Actually useles...
function logout ($req, $res) {
  // Destroy the token
}

// Return true if the $req has a valid token
function isLogged ($req) {
  $token = getToken($req);
  return ($token && $token["user_id"]);
}

// Return the user.id of the logged user or null
function getUserId ($req) {
  $token = getToken($req);
  if ($token && $token["user_id"]) {
    return $token["user_id"];
  } else {
    return null;
  }
}

function getRights ($req) {
  // Return an array of the right of the user
}
