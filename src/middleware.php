<?php

// > Middlewares
//  - Add "->add($mwXXX)" after a route definition
// to use the "$mwXXX" for that specific route.
//  - All middleware are variables with the "mw"
// prefix.

function mwIsLogged ($request, $response, $next) {
  $token = jwt\getToken($request);
  if ($token) {
    return $next($request, $response);
  } else {
    return $response->withStatus(401);
  }
};

function mwIsFarmer ($request, $response, $next) {
  $token = jwt\getToken($request);
  if ($token && $token["user_type"] == "farmer") {
    return $next($request, $response);
  } else {
    return $response->withStatus(401);
  }
};

function mwIsAdmin ($request, $response, $next) {
  $token = jwt\getToken($request);
  if ($token && $token["user_type"] == "admin") {
    return $next($request, $response);
  } else {
    return $response->withStatus(401);
  }
};
