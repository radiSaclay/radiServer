<?php namespace auth;

  // Logs the user who made the request in using the requests JsonWebToken
function login ($request, $response) {
  $body = $request->getParsedBody();
  $user = \UserQuery::create()->findOneByEmail($body["email"]);
  if ($user && password_verify($body["password"], $user->getPassword())) {
    return $response->withJson([ "validated" => true, "token" => createUserToken($user) ]);
  } else {
    return $response->withJson([ "validated" => false, "msg" => "Wrong credentials" ], 401);
  }
}

  // Finds user associated with the JWT contained in the request
function getUser ($request) {
  $token = \jwt\getToken($request);
  if ($token && isset($token["user_id"])) {
    $user = \UserQuery::create()->findPK($token["user_id"]);
    return $user ? $user : null;
  } else {
    return null;
  }
}

  // Checks if the request contains a valid users JWT
function isUser ($request) {
  return (getUser($request) != null);
}

  // Gets the farm associated to $user
function getUserFarm ($user) {
  return isUserFarmer($user)
    ? $user->getFarms()->getFirst()
    : null;
}

  // Checks if the user has any farms
function isUserFarmer ($user) {
  return ($user && $user->countFarms() > 0);
}

  // Checks if the user has admin privileges
function isUserAdmin ($user) {
  return ($user && $user->getIsAdmin());
}

  // Returns the farm associated to the user
function getFarm ($request) {
  $user = getUser($request);
  return getUserFarm($user);
}

  // Checks if the user who made the request has any farms
function isFarmer ($request) {
  $user = getUser($request);
  return isUserFarmer($user);
}

  // Checks if the user who made the request has admin privileges
function isAdmin ($request) {
  $user = getUser($request);
  return isUserAdmin($user);
}

// ===

  // Creates a JsonWebToken associated to the given user
function createUserToken ($user) {
  $lifespan = CONFIG["TOKEN_LIFESPAN"];
  return \jwt\createToken([
    "user_id" => $user->getId(),
  ], $lifespan);
}
