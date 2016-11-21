<?php namespace api;

// Get query objects
function query ($table) {
  if ($table == 'authors') return \AuthorQuery::create();
  if ($table == 'books') return \BookQuery::create();
  if ($table == 'publishers') return \PublisherQuery::create();
}

// Get instance objects
function instanciate ($table) {
  if ($table == 'authors') return new \Author();
  if ($table == 'books') return new \Book();
  if ($table == 'publishers') return new \Publisher();
}

// ==================================================
//  > Resource
// ==================================================
function resource ($table) {
  return function ($req, $res, $args) use ($table) {
    $method = $req->getMethod();
    $id = isset($args['id']) ? $args['id'] : null;
    $result = array('status' => 404, 'data' => []);

    if ($id == null) {
      if ($method == 'GET') $result = getAll($table);
      if ($method == 'POST') $result = create($table, $req);
    } else {
      if ($method == 'GET') $result = getById($table, $id);
      if ($method == 'PUT') $result = edit($table, $id, $req);
      // if ($method == 'DELETE') $result = destroy($table, $id);
    }

    return $res
      ->withStatus($result['status'])
      ->withJson($result['data']);
  };
}

function getAll ($table) {
  return array('status' => 200, 'data' => query($table)->find()->toArray());
}

function create ($table, $req) {
  $item = instanciate($table);
  $item->fromArray($req->getParsedBody());
  $item->save();
  return array('status' => 200, 'data' => $item->toArray());
}

function getById ($table, $id) {
  if ($item = query($table)->findPK($id)) {
    return array('status' => 200, 'data' => $item->toArray());
  } else {
    return array('status' => 404, 'data' => null);
  }
}

function edit ($table, $id, $req) {
  if ($item = query($table)->findPK($id)) {
    $item->update($req->getParsedBody());
    return array('status' => 200, 'data' => $item->toArray());
  } else {
    return array('status' => 404, 'data' => null);
  }
}
