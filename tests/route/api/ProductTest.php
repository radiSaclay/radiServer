<?php

require_once 'tools/seeder.php';
require_once 'tools/faker.php';

final class RouteApiProductTest extends ServerTestCase {

  // = Helpers ===

  public function getAdminToken () {
    $admin = faker\makeAdmin();
    return auth\createUserToken($admin);
  }

  public function checkProduct ($product, $productData) {
    $this->assertEquals($product->getName(), $productData['name']);
    if (isset($productData['id']))
      $this->assertEquals($product->getId(), $productData['id']);
  }

  // = Tests ===

  public function testRoutePostProduct () {
    $data = faker\productData();

    $res = makeRequest(
      'POST', '/api/products/', $data,
      ['HTTP_AUTHORIZATION' => $this->getAdminToken()]
    );
    $this->assertEquals($res->getStatusCode(), 200);

    $product = ProductQuery::create()->filterByName($data['name'])->findOne();
    $this->assertTrue($product != null);
    $this->checkProduct($product, $data);
  }

  public function testRouteGetProductById () {
    $data = faker\productData();
    $product = seeder\makeProduct($data);

    $res = makeRequest('GET', '/api/products/' . $product->getId());
    $this->assertEquals($res->getStatusCode(), 200);

    $body = json_decode($res->getBody(), true);
    $this->checkProduct($product, $body);
  }

  public function testRouteGetProducts () {
    for ($i = 0; $i < 10; $i++)
      faker\makeProduct();

    $res = makeRequest('GET', '/api/products/');
    $this->assertEquals($res->getStatusCode(), 200);
  }

  public function testRouteUpdateProduct () {
    $product = faker\makeProduct();
    $data = faker\productData();

    $res = makeRequest(
      'PUT', '/api/products/' . $product->getId(), $data,
      ['HTTP_AUTHORIZATION' => $this->getAdminToken()]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->checkProduct($product, $data);
  }

  public function testRouteDeleteProduct () {
    $product = faker\makeProduct();

    $res = makeRequest(
      'DELETE', '/api/products/' . $product->getId(), null,
      ['HTTP_AUTHORIZATION' => $this->getAdminToken()]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->assertEquals(ProductQuery::create()->findPK($product->getId()), null);
  }

  public function testRouteSubscribeFarm () {
    $product = faker\makeProduct();
    $user = faker\makeUser();
    $token = auth\createUserToken($user);

    $res = makeRequest(
      'POST', '/api/products/subscribe/' . $product->getId(), null,
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->assertTrue($product->hasSubscriber($user));
  }

  public function testRouteUnsubscribeFarm () {
    $product = faker\makeProduct();
    $user = faker\makeUser();
    $token = auth\createUserToken($user);

    $product->addSubscriber($user);
    $this->assertTrue($product->hasSubscriber($user));

    $res = makeRequest(
      'POST', '/api/products/unsubscribe/' . $product->getId(), null,
      ['HTTP_AUTHORIZATION' => $token]
    );
    $this->assertEquals($res->getStatusCode(), 200);
    $this->assertFalse($product->hasSubscriber($user));
  }

}
