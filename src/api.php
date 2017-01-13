<?php namespace api;
use Exception;

function nullFunction ($item) { return []; }

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

function mapCollectionNoResponse($list, $callback){
  $data = [];
  foreach($list as $item) $data[] = $callback($item);
  return $data;
}

function view ($response, $item) {
  if (is_callable([$item, 'serialize'])) {
    return $item
      ? $response->withJson($item->serialize(), 200)
      : $response->withStatus(404);
  }else{
    return $item
      ? $response->withJson($item, 200)
      : $response->withStatus(404);
  }
}

function listCollection ($request, $response, $query, $callback = '\api\nullFunction') {
  $list = getCollection($request, $query);
  $main_detail_lvl = $request->getParam('main_detail_lvl');
  $embedded_detail_lvl = $request->getParam('embedded_detail_lvl');
  $data = [];
  foreach($list as $item) $data[] = array_merge(
    $item->serialize($main_detail_lvl, $embedded_detail_lvl),
    $callback($item)
  );
  return $response->withJson($data, 200);
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
