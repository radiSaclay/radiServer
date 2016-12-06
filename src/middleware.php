<?php

// > Middlewares
//  - Add "->add($mwXXX)" after a route definition
// to use the "$mwXXX" for that specific route.
//  - All middleware are variables with the "mw"
// prefix.

// ==================================================
// > Check Logged
// --------------------------------------------------
//   Middleware that check if the user is logged.
// Returns 401 status for guests.
// ==================================================
$mwCheckLogged = function ($req, $res, $next) {
  if (auth\isLogged($req)) {
    return $next($req, $res);
  } else {
    return $res->withStatus(401);
  }
};
