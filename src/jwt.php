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
  $body["iat"] = $now;
  $body["exp"] = $now + $lifespan;
  return JWT::encode($body, $jwtkey);
}

function decodeToken ($jwt) {
  global $jwtkey;
  return (array) JWT::decode($jwt, $jwtkey, array('HS256'));
}

function getToken ($req) {
  if ($req->hasHeader('Authorization')) {
    return decodeToken($req->getHeader('Authorization')[0]);
  } else {
    return null;
  }
}
