<?php namespace auth;

use \Firebase\JWT\JWT;
// $key = "example_key";
// $token = array(
//   "iss" => issuer
//   "aud" => audience
//   "iat" => issued at
//   "nbf" => not before
//   "exp" => expire at
// );
// JWT::encode( ... , $key);
// JWT::decode( ... , $key, array('HS256'));

function signin ($req, $res) {
  // Create the new user and save it
}

function login ($req, $res) {
  // Verify the user credentials, create a token and send it back
}

function logout ($req, $res) {
  // Destroy the token
}

function isLogged ($req) {
  // Return true if the $req has a valid token
}

function getRights ($req) {
  // Return an array of the right of the user
}
