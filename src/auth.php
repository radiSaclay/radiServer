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
  $lifespan = 60 * 60 * 24;
  if (isUserFarmer($user)) {
    return \jwt\createToken([
      "user_id" => $user->getId(),
      "user_type" => "farmer",
      "farm_id" => getUserFarm($user)->getId(),
    ], $lifespan);
  } else {
    return \jwt\createToken([
      "user_id" => $user->getId(),
      "user_type" => isUserAdmin($user) ? "admin" : "farmer",
    ], $lifespan);
  }
}
