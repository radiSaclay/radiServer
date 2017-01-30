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
    $response2 = makeRequest('POST', '/api/products/', ['name' => 'This is ',
      'parentId' => null
      ]);
    $response = makeRequest('POST', '/auth/signup', ['email' => 'mymail',
      'password' => 'adsd'
    ]);



    echo "\n";
    echo $response2->getBody();
    echo $response2->getStatusCode();
    echo $response->getBody();
    echo $response->getStatusCode();
    echo $res->getBody();
    echo "\n";
  }

}


