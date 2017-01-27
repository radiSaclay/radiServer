<?php
require_once __DIR__ . "/bootstrap.php";
function makeEvent ($title, $content) {
  $event = new Event();
  $event->setTitle($title);
  $event->setDescription($content);
  $event->save();
  return $event;
}

final class EventApiTest extends ServerTestCase {

  public function testGetAll () {
    $this->assertEquals(2,  1 + 1);

    $res = makeRequest('GET', '/api/events/');
    makeEvent('a', 'aaa');
    makeEvent('b', 'bbb');
    $response = makeRequest('POST', '/api/products/', ['name' => 'This is ',
      'parentId' => null
      ]);


    echo "\n";
    echo $response->getBody();
    echo $response->getStatusCode();
    echo $res->getBody();
    echo "\n";
  }

}


