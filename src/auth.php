<?php namespace auth;

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
