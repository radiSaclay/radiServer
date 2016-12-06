<?php namespace auth;

require_once 'jwt.php';

// ==================================================
// > createUserToken
// --------------------------------------------------
//   Returns an encoded jwt string containing the
// "user_id" and lasting one week.
// ==================================================
function createUserToken (\User $user) {
  return createToken([
    "user_id" => $user->getId()
  ], 60 * 60 * 24 * 7);
}

// ==================================================
// > createUserToken
// --------------------------------------------------
//   Returns true if the given request contains
// a valid token with a "user_id".
// ==================================================
function isLogged ($request) {
  $token = getToken($request);
  return ($token && $token["user_id"]);
}

// ==================================================
// > createUserToken
// --------------------------------------------------
//   Returns the user.id of the logged user or null
// if no user is logged.
// ==================================================
function getUserId ($request) {
  $token = getToken($request);
  if ($token && $token["user_id"]) {
    return $token["user_id"];
  } else {
    return null;
  }
}
