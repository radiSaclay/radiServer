<?php namespace auth;

// > Load JWT library
use \Firebase\JWT\JWT;

// > Load the JWT key
// "JWTKEY" from the "./.env" file
// $jwtkey = $_ENV["JWTKEY"];
$jwtkey = "raphanus-sativus";

// ==================================================
// > createToken
// --------------------------------------------------
//   Returns an encoded jwt string with the given
// payload and the given lifespan. Adds all
// attributes that will be useful for checking.
// ==================================================
function createToken ($payload, $lifespan = 0) {
  global $jwtkey;
  $now = time();
  return JWT::encode(array_merge(
    $payload, [
      "iat" => $now, // issued at
      "nbf" => $now, // not before
      "exp" => $now + $lifespan, // expire at
      "iss" => $_SERVER["SERVER_ADDR"] // issuer
    ]
  ), $jwtkey);
}

// ==================================================
// > getAuthJWT
// --------------------------------------------------
//   Returns the jwt string contained in the given
// request"s header under "Authorization". Returns
// null if there is not.
// ==================================================
function getAuthJWT ($request) {
  if ($request->hasHeader("Authorization")) {
    return $request->getHeader("Authorization")[0];
  } else {
    return null;
  }
}

// ==================================================
// > decodeToken
// --------------------------------------------------
//   Decodes an jwt string and returns its payload
// as an array.
// ==================================================
function decodeToken ($jwt) {
  global $jwtkey;
  return (array) JWT::decode($jwt, $jwtkey, array("HS256"));
}

// ==================================================
// > checkToken
// --------------------------------------------------
//   Check if the given payload belongs to a valid
// token.
// ==================================================
function checkToken ($token) {
  $now = time();
  if (!isset($token["iat"])) return false;
  if (!isset($token["nbf"]) || $now < $token["nbf"]) return false;
  if (!isset($token["exp"]) || $now > $token["exp"]) return false;
  if (!isset($token["iss"]) || $token["iss"] != $_SERVER["SERVER_ADDR"]) return false;
  return true;
}

// ==================================================
// > getToken
// --------------------------------------------------
//   Check if the given request has a valid token and
// returns it if so. Returns null in any other case.
// ==================================================
function getToken ($request) {
  $jwt = getAuthJWT($request);
  if ($jwt != null) {
    $token = decodeToken($jwt);
    if (checkToken($token)) {
      return $token;
    }
  }
  return null;
}
