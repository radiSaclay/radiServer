<?php

require_once 'tools/seeder.php';
require_once 'tools/faker.php';

final class RouteApiEventTest extends ServerTestCase {

  // = Helpers ===

  public function checkEvent ($event, $eventData) {
    // var_dump($eventData);
    $this->assertEquals($event->getTitle(), $eventData['title']);
    $this->assertEquals($event->getDescription(), $eventData['description']);
    $this->assertEquals($event->getPublishAt('U'), $eventData['publishAt']);
    $this->assertEquals($event->getBeginAt('U'), $eventData['beginAt']);
    $this->assertEquals($event->getEndAt('U'), $eventData['endAt']);
    if (isset($eventData['id']))
      $this->assertEquals($event->getId(), $eventData['id']);
  }

  // = Tests ===

  public function testRoutePostEvent () {
    $user = faker\makeUser();
    $farm = faker\makeFarm($user);
    $token = auth\createUserToken($user);
    $eventData = faker\eventData();

    $res = makeRequest(
      'POST', '/api/events/', $eventData,
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);

    $event = EventQuery::create()->filterByTitle($eventData['title'])->findOne();
    $this->assertTrue($event != null);
    $this->checkEvent($event, $eventData);
  }

  public function testRouteGetEventById () {
    $eventData = faker\eventData();
    $event = seeder\makeEvent($eventData);

    $res = makeRequest('GET', '/api/events/' . $event->getId());
    $this->assertEquals($res->getStatusCode(), 200);

    $body = json_decode($res->getBody(), true);
    $this->checkEvent($event, $body);
  }

  public function testRouteGetEvents () {
    for ($i = 0; $i < 10; $i++)
      faker\makeEvent();
    $res = makeRequest('GET', '/api/events/');
    $this->assertEquals($res->getStatusCode(), 200);
  }

  public function testRouteUpdateEvent () {
    $user = faker\makeUser();
    $farm = faker\makeFarm($user);
    $token = auth\createUserToken($user);
    $event = faker\makeEvent($farm);
    $eventData = faker\eventData();

    $res = makeRequest(
      'PUT', '/api/events/' . $event->getId(), $eventData,
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->checkEvent($event, $eventData);
  }

  public function testRouteDeleteEvent () {
    $user = faker\makeUser();
    $farm = faker\makeFarm($user);
    $token = auth\createUserToken($user);
    $event = faker\makeEvent($farm);

    $res = makeRequest(
      'DELETE', '/api/events/' . $event->getId(), null,
      ['HTTP_AUTHORIZATION' => $token]
    );

    $this->assertEquals($res->getStatusCode(), 200);
    $this->assertEquals(EventQuery::create()->findPK($event->getId()), null);
  }

  public function testRouteGetEventsByFarmId () {
    $farm = faker\makeFarm(faker\makeUser());
    $event1 = faker\makeEvent($farm);
    $event2 = faker\makeEvent(faker\makeFarm(faker\makeUser()));
    $event3 = faker\makeEvent();

    $res = makeRequest('GET', '/api/events/?farmId=' . $farm->getId());
    $this->assertEquals($res->getStatusCode(), 200);

    $body = json_decode($res->getBody(), true);
    $list = array_map(function ($event) { return $event['id']; }, $body);
    $this->assertContains($event1->getId(), $list);
    $this->assertNotContains($event2->getId(), $list);
    $this->assertNotContains($event3->getId(), $list);
  }

  public function testRouteGetEventsBySubscribed () {
    $farm = faker\makeFarm(faker\makeUser());
    $event1 = faker\makeEvent($farm);
    $event2 = faker\makeEvent(faker\makeFarm(faker\makeUser()));
    $event3 = faker\makeEvent();

    $user = faker\makeUser();
    $token = auth\createUserToken($user);

    $farm->addSubscriber($user);
    $this->assertTrue($farm->hasSubscriber($user));

    $res = makeRequest(
      'GET', '/api/events/?subscribed=1', null,
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);

    $body = json_decode($res->getBody(), true);
    $list = array_map(function ($event) { return $event['id']; }, $body);
    $this->assertContains($event1->getId(), $list);
    $this->assertNotContains($event2->getId(), $list);
    $this->assertNotContains($event3->getId(), $list);
  }

  public function testUpdatingProductsOfEvents () {
    $product1 = faker\makeProduct();
    $product2 = faker\makeProduct();
    $product3 = faker\makeProduct();

    $user = faker\makeUser();
    $farm = faker\makeFarm($user);
    $token = auth\createUserToken($user);
    $event = faker\makeEvent($farm);

    $event->addProduct($product1);
    $event->addProduct($product2);
    $this->assertTrue($event->hasProduct($product1));
    $this->assertTrue($event->hasProduct($product2));
    $this->assertFalse($event->hasProduct($product3));

    $res = makeRequest(
      'PUT', '/api/events/' . $event->getId(),
      ['products' => [$product2->getid(), $product3->getid()]],
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);

    $body = json_decode($res->getBody(), true);
    $this->assertNotContains($product1->getId(), $body['products']);
    $this->assertContains($product2->getId(), $body['products']);
    $this->assertContains($product3->getId(), $body['products']);
  }

}
