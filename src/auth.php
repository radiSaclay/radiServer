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

function logout ($req, $res) {
  // Destroy the token
}

function isLogged ($req) {
  // Return true if the $req has a valid token
}

function getRights ($req) {
  // Return an array of the right of the user
}
