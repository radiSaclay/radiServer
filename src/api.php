<?php namespace api;

function mapCollection ($response, $list, $callback) {
  $data = [];
  foreach($list as $item) $data[] = $callback($item);
  return $response->withJson($data, 200);
}

function view ($response, $item) {
  return $item
    ? $response->withJson($item->serialize(), 200)
    : $response->withStatus(404);
}

function update ($request, $response, $item) {
  $item->unserialize($request->getParsedBody());
  $item->save();
  return $response->withJson($item->serialize(), 200);
}
