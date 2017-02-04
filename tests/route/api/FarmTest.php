<?php

require_once 'tools/seeder.php';

final class RouteApiFarmTest extends ServerTestCase {

  // = Helpers ===

  public function randFarmData () {
    $faker = Faker\Factory::create();
    return [
      'name' => $faker->word,
      'address' => $faker->address,
      'website' => $faker->url,
      'phone' => $faker->e164PhoneNumber,
      'email' => $faker->email,
    ];
  }

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
    $faker = Faker\Factory::create();
    $user = seeder\makeUser($faker->email, $faker->word);
    $token = auth\createUserToken($user);
    $farmData = $this->randFarmData();

    $res = makeRequest('POST', '/api/farms/', $farmData, ['HTTP_AUTHORIZATION' => $token]);
    $this->assertEquals($res->getStatusCode(), 200);

    $farm = auth\getUserFarm($user);
    $this->assertTrue($farm != null);
    $this->checkFarm($farm, $farmData);
  }

  public function testRouteGetFarmById () {
    $faker = Faker\Factory::create();
    $farmData = $this->randFarmData();
    $name = $farmData['name'];
    $user = seeder\makeUser($faker->email, $faker->word);
    $farm = seeder\makeFarm($user, $name, $farmData);

    $res = makeRequest('GET', '/api/farms/' . $farm->getId());
    $this->assertEquals($res->getStatusCode(), 200);

    $body = json_decode($res->getBody(), true);
    $this->checkFarm($farm, $body);
  }

  public function testRouteGetFarms () {
    $faker = Faker\Factory::create();
    for ($i = 0; $i < 10; $i++) {
      $user = seeder\makeUser($faker->email, $faker->word);
      $farm = seeder\makeFarm($user, $faker->word, $this->randFarmData());
    }
    $res = makeRequest('GET', '/api/farms/');
    $this->assertEquals($res->getStatusCode(), 200);
  }

  public function testRouteUpdateFarm () {
    $faker = Faker\Factory::create();
    $user = seeder\makeUser($faker->email, $faker->word);
    $farm = seeder\makeFarm($user, $faker->word, $this->randFarmData());
    $token = auth\createUserToken($user);
    $farmData = $this->randFarmData();

    $res = makeRequest('PUT', '/api/farms/' . $farm->getId(), $farmData, ['HTTP_AUTHORIZATION' => $token]);
    $this->assertEquals($res->getStatusCode(), 200);

    $this->checkFarm($farm, $farmData);
  }

  public function testRouteDeleteFarm () {
    $faker = Faker\Factory::create();
    $user = seeder\makeUser($faker->email, $faker->word);
    $farm = seeder\makeFarm($user, $faker->word, $this->randFarmData());
    $token = auth\createUserToken($user);

    $res = makeRequest('DELETE', '/api/farms/' . $farm->getId(), null, ['HTTP_AUTHORIZATION' => $token]);
    $this->assertEquals($res->getStatusCode(), 200);

    $this->assertEquals(FarmQuery::create()->findPK($farm->getId()), null);
  }

}
