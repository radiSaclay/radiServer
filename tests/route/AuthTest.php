<?php

require_once 'tools/seeder.php';
require_once 'tools/faker.php';

final class RouteAuthTest extends ServerTestCase {

  public function testSignup () {
    $email = faker\email();
    $password = faker\word();

    $res = makeRequest('POST', '/auth/signup', [
      "email" => $email,
      "password" => $password,
    ]);
    $this->assertEquals($res->getStatusCode(), 200);

    $user = UserQuery::create()->filterByEmail($email)->findOne();
    $this->assertTrue($user != null);
    $this->assertTrue(password_verify($password, $user->getPassword()));

    $body = json_decode($res->getBody(), true);
    $this->assertArrayHasKey('token', $body);
    $token = jwt\decodeToken($body['token']);
    $this->assertEquals($token['user_id'], $user->getId());
  }

  public function testLogin () {
    $email = faker\email();
    $password = faker\word();
    $user = seeder\makeUser($email, $password);

    $res = makeRequest('POST', '/auth/login', [
      "email" => $email,
      "password" => $password,
    ]);
    $this->assertEquals($res->getStatusCode(), 200);

    $body = json_decode($res->getBody(), true);
    $this->assertArrayHasKey('token', $body);
    $token = jwt\decodeToken($body['token']);
    $this->assertEquals($token['user_id'], $user->getId());
  }

  public function testGetUser () {
    $email = faker\email();
    $password = faker\word();
    $user = seeder\makeUser($email, $password);
    $token = auth\createUserToken($user);

    $res = makeRequest('GET', '/auth/user', null, [
      'HTTP_AUTHORIZATION' => $token,
    ]);
    $this->assertEquals($res->getStatusCode(), 200);
    $body = json_decode($res->getBody(), true);

    $this->assertArrayHasKey('id', $body);
    $this->assertEquals($body['id'], $user->getId());

    $this->assertArrayHasKey('email', $body);
    $this->assertEquals($body['email'], $user->getEmail());
  }

}
