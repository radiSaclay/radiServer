<?php namespace auth;

use \Firebase\JWT\JWT;
// $token = array(
//   "iss" => issuer
//   "aud" => audience
//   "iat" => issued at
//   "nbf" => not before
//   "exp" => expire at
// );
// JWT::encode( ... , $jwtkey);
// JWT::decode( ... , $jwtkey, array('HS256'));

// Create a token for a specific user
function createToken (\User $user) {
  $jwtkey = $_ENV['JWTKEY'];
  $now = time();
  return JWT::encode([
    "iat" => $now,
    "exp" => $now + (60 * 60 * 24 * 7), // 1 week
    "user_id" => $user->getId()
  ], $jwtkey);
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
      "token" => createToken($user)
    ]);
}

// Verify the user credentials, create a token and send it back
function login ($req, $res) {
  $data = $req->getParsedBody();
  $user = \UserQuery::create()->findOneByEmail($data["Email"]);
  if ($user && $user->getPassword() === $data["Password"]) {
    return $res->withJson([
      "validated" => true,
      "token" => createToken($user)
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
