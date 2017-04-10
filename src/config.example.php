<?php

const CONFIG = [
  "JWTKEY" => "secret", // Secret key used to sign the JsonWebToken
  "TOKEN_LIFESPAN" => 60 * 60 * 24, // One day
  "MAX_LIMIT" => 20, // Limit of item to send back in one request (pagination)
];
