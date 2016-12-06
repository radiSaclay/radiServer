<?php

// > API
//  ~ GET    /api/XXXs/ -> list all
//  ~ GET    /api/XXXs/:id -> get one by id
//  ~ POST   /api/XXXs/ -> create new one
//  ~ PUT    /api/XXXs/:id -> update by id
//  ~ DELETE /api/XXXs/:id -> delete by id
$app->any('/api/farms/[{id}]', api\resource('farms'))->add($mwCheckLogged);
