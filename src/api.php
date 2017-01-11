<?php namespace api;
use Exception;

function getCollection ($request, $query) {
  $offset = $request->getParam('offset');
  $limit = $request->getParam('limit');
  if ($limit == null || $limit > CONFIG["MAX_LIMIT"]) {
    $limit = CONFIG["MAX_LIMIT"];
  }
  return $query->paginate($offset, $limit);
}

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
  if(!$item->validate()){
    foreach ($item->getValidationFailures() as $failure) {
      throw new Exception("Property: ".$failure->getPropertyPath()." failed the following test: ".$failure->getMessage()."\n");
    }
  }
  $item->save();
  return $response->withJson($item->serialize(), 200);
}
