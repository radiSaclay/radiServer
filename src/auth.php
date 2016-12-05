<?php namespace auth;

require_once 'jwt.php';

// Create a token for a specific user
function createUserToken (\User $user) {
  return createToken([
    "user_id" => $user->getId()
  ], 60 * 60 * 24 * 7);
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
