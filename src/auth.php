<?php namespace auth;

function getUser ($request) {
  $token = \jwt\getToken($request);
  if ($token && isset($token["user_id"])) {
    $user = \UserQuery::create()->findPK($token["user_id"]);
    return $user ? $user : null;
  } else {
    return null;
  }
}

function isUser ($request) {
  return (getUser($request) != null);
}

function getUserFarm ($user) {
  return isUserFarmer($user)
    ? $user->getFarms()->getFirst()
    : null;
}

function isUserFarmer ($user) {
  return ($user && $user->countFarms() > 0);
}

function isUserAdmin ($user) {
  return ($user && $user->getIsAdmin());
}

function getFarm ($request) {
  $user = getUser($request);
  return getUserFarm($user);
}

function isFarmer ($request) {
  $user = getUser($request);
  return isUserFarmer($user);
}

function isAdmin ($request) {
  $user = getUser($request);
  return isUserAdmin($user);
}

// ===

function createUserToken ($user) {
  $lifespan = CONFIG["TOKEN_LIFESPAN"];
  return \jwt\createToken([
    "user_id" => $user->getId(),
  ], $lifespan);
}
