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

$jwtkey = $_ENV['JWTKEY'];

function createToken ($body, $lifespan = 0) {
  global $jwtkey;
  $now = time();
  return JWT::encode(array_merge(
    $body, [
      "iat" => $now, // issued at
      "nbf" => $now, // not before
      "exp" => $now + $lifespan, // expire at
      "iss" => $_SERVER["SERVER_ADDR"] // issuer
    ]
  ), $jwtkey);
}

function getAuthJWT ($req) {
  if ($req->hasHeader('Authorization')) {
    return $req->getHeader('Authorization')[0];
  } else {
    return null;
  }
}

function decodeToken ($jwt) {
  global $jwtkey;
  return (array) JWT::decode($jwt, $jwtkey, array('HS256'));
}

function checkToken ($token) {
  $now = time();
  if (!isset($token["iat"])) return false;
  if (!isset($token["nbf"]) || $now < $token["nbf"]) return false;
  if (!isset($token["exp"]) || $now > $token["exp"]) return false;
  if (!isset($token["iss"]) || $token["iss"] != $_SERVER["SERVER_ADDR"]) return false;
  return true;
}

function getToken ($req) {
  $jwt = getAuthJWT($req);
  if ($jwt != null) {
    $token = decodeToken($jwt);
    if (checkToken($token)) {
      return $token;
    }
  }
  return null;
}
