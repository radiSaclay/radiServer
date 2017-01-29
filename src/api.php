<?php namespace api;
use Exception;

// = Helpers ===
function nullFunction ($item) {}

function getCollection ($request, $query) {
  $offset = $request->getParam('offset');
  $limit = $request->getParam('limit');
  if ($limit == null || $limit > CONFIG["MAX_LIMIT"]) {
    $limit = CONFIG["MAX_LIMIT"];
  }
  return $query->paginate($offset, $limit);
}

function getParams ($request) {
  $level = $request->getParam('details');
  $level = ($level != null) ? intval($level) : 1;
  $embedded_level = $request->getParam('embedded') ? 0 : -1;
  return [
    "level" => $level,
    "embedded_level" => $embedded_level,
  ];
}

function serializeItem ($item, $callback, $level, $embedded_level, $request) {
  $base_data = $item->serialize($level, $embedded_level, $request);
  $more_data = $callback($item);
  return $more_data
    ? array_merge($base_data, $more_data)
    : $base_data;
}

// = CRUD ===
function listCollection ($request, $response, $query, $callback = '\api\nullFunction') {
  $params = getParams($request);
  $data = \collection\map(
    getCollection($request, $query),
    function ($item) use ($params, $callback, $request) {
      return serializeItem($item, $callback, $params["level"], $params["embedded_level"], $request);
    }
  );
  return $response->withJson($data, 200);
}

function view ($request, $response, $item, $callback = '\api\nullFunction') {
  if (!$item) return $response->withStatus(404);
  $data = serializeItem($item, $callback, 2, 0, $request);
  return $response->withJson($data, 200);
}

function update ($request, $response, $item) {
  try {
    $item->unserialize($request->getParsedBody());
    if (!$item->validate()) {
      $errors = [];
      foreach ($item->getValidationFailures() as $failure)
        $errors[] = "Property ".$failure->getPropertyPath().": ".$failure->getMessage()."\n";
      return $response->withJson([ "errors" => $errors ], 400);
    }
    $item->save();
    return $response->withJson($item->serialize(), 200);
  } catch (Exception $e) {
    return $response->withJson([ "errors" => [$e->getMessage()] ], 400);
  }
}

function delete ($request, $response, $item) {
  try {
    $item->delete();
    return $response->withStatus(200);
  } catch (Exception $e) {
    return $response->withJson([ "errors" => [$e->getMessage()] ], 400);
  }

}
