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
  if (\auth\isFarmer($request)) {
    return $next($request, $response);
  } else {
    return $response->withStatus(401);
  }
};

function mwIsAdmin ($request, $response, $next) {
  if (\auth\isAdmin($request)) {
    return $next($request, $response);
  } else {
    return $response->withStatus(401);
  }
};
