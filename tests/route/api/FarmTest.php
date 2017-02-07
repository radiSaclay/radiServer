<?php

require_once 'tools/seeder.php';
require_once 'tools/faker.php';

final class RouteApiFarmTest extends ServerTestCase {

  // = Helpers ===

  public function checkFarm ($farm, $farmData) {
    $this->assertEquals($farm->getName(), $farmData['name']);
    $this->assertEquals($farm->getAddress(), $farmData['address']);
    $this->assertEquals($farm->getWebsite(), $farmData['website']);
    $this->assertEquals($farm->getPhone(), $farmData['phone']);
    $this->assertEquals($farm->getEmail(), $farmData['email']);
    if (isset($farmData['id']))
      $this->assertEquals($farm->getId(), $farmData['id']);
  }

  // = Tests ===

  public function testRoutePostFarm () {
    $user = faker\makeUser();
    $token = auth\createUserToken($user);
    $farmData = faker\farmData();

    $res = makeRequest(
      'POST', '/api/farms/', $farmData,
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);

    $farm = auth\getUserFarm($user);
    $this->assertTrue($farm != null);
    $this->checkFarm($farm, $farmData);
  }

  public function testRouteGetFarmById () {
    $faker = Faker\Factory::create();
    $farmData = faker\farmData();
    $user = faker\makeUser();
    $farm = seeder\makeFarm($user, $farmData);

    $res = makeRequest('GET', '/api/farms/' . $farm->getId());
    $this->assertEquals($res->getStatusCode(), 200);

    $body = json_decode($res->getBody(), true);
    $this->checkFarm($farm, $body);
  }

  public function testRouteGetFarms () {
    for ($i = 0; $i < 10; $i++)
      faker\makeFarm(faker\makeUser());
    $res = makeRequest('GET', '/api/farms/');
    $this->assertEquals($res->getStatusCode(), 200);
  }

  public function testRouteUpdateFarm () {
    $user = faker\makeUser();
    $farm = faker\makeFarm($user);
    $token = auth\createUserToken($user);
    $farmData = faker\farmData();

    $res = makeRequest(
      'PUT', '/api/farms/' . $farm->getId(), $farmData,
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->checkFarm($farm, $farmData);
  }

  public function testRouteDeleteFarm () {
    $user = faker\makeUser();
    $farm = faker\makeFarm($user);
    $token = auth\createUserToken($user);

    $res = makeRequest(
      'DELETE', '/api/farms/' . $farm->getId(), null,
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->assertEquals(FarmQuery::create()->findPK($farm->getId()), null);
  }

  public function testRouteSubscribeFarm () {
    $farm = faker\makeFarm(faker\makeUser());
    $user = faker\makeUser();
    $token = auth\createUserToken($user);

    $res = makeRequest(
      'POST', '/api/farms/subscribe/' . $farm->getId(), null,
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->assertTrue($farm->hasSubscriber($user));
  }

  public function testRouteUnsubscribeFarm () {
    $farm = faker\makeFarm(faker\makeUser());
    $user = faker\makeUser();
    $token = auth\createUserToken($user);

    $farm->addSubscriber($user);
    $this->assertTrue($farm->hasSubscriber($user));

    $res = makeRequest(
      'POST', '/api/farms/unsubscribe/' . $farm->getId(), null,
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->assertFalse($farm->hasSubscriber($user));
  }

}
