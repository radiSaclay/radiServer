<?php

require_once 'tools/seeder.php';

final class RouteApiProductTest extends ServerTestCase {

  // = Helpers ===

  public function getAdminToken () {
    $faker = Faker\Factory::create();
    $admin = seeder\makeAdmin($faker->email, $faker->word);
    return auth\createUserToken($admin);
  }

  // = Tests ===

  public function testRoutePostProduct () {
    $faker = Faker\Factory::create();
    $name = $faker->word;

    $res = makeRequest(
      'POST', '/api/products/',
      ['name' => $name],
      ['HTTP_AUTHORIZATION' => $this->getAdminToken()]
    );
    $this->assertEquals($res->getStatusCode(), 200);

    $product = ProductQuery::create()->filterByName($name)->findOne();
    $this->assertTrue($product != null);
  }

  public function testRouteGetProductById () {
    $faker = Faker\Factory::create();
    $name = $faker->word;
    $product = seeder\makeProduct($name);

    $res = makeRequest('GET', '/api/products/' . $product->getId());
    $this->assertEquals($res->getStatusCode(), 200);

    $body = json_decode($res->getBody(), true);
    $this->assertEquals($name, $body['name']);
  }

  public function testRouteGetProducts () {
    $faker = Faker\Factory::create();
    for ($i = 0; $i < 10; $i++)
      seeder\makeProduct($faker->word);

    $res = makeRequest('GET', '/api/products/');
    $this->assertEquals($res->getStatusCode(), 200);
  }

  public function testRouteUpdateProduct () {
    $faker = Faker\Factory::create();
    $product = seeder\makeProduct($faker->word);
    $name = $faker->word;

    $res = makeRequest(
      'PUT', '/api/products/' . $product->getId(),
      ['name' => $name],
      ['HTTP_AUTHORIZATION' => $this->getAdminToken()]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->assertEquals($name, $product->getName());
  }

  public function testRouteDeleteProduct () {
    $faker = Faker\Factory::create();
    $product = seeder\makeProduct($faker->word);

    $res = makeRequest(
      'DELETE', '/api/products/' . $product->getId(), null,
      ['HTTP_AUTHORIZATION' => $this->getAdminToken()]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->assertEquals(ProductQuery::create()->findPK($product->getId()), null);
  }

}
