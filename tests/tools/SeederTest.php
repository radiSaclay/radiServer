<?php

use PHPUnit\Framework\TestCase;
require_once 'tools/seeder.php';
require_once 'tools/faker.php';

final class SeederTest extends ServerTestCase {

  // = USERS ===

  public function testMakeAdmin () {
    $email = faker\email();
    $password = faker\word();

    $id = seeder\makeAdmin($email, $password)->getId();
    $user = \UserQuery::create()->findPK($id);

    $this->assertTrue($user != null);
    $this->assertEquals($user->getEmail(), $email);
    $this->assertEquals($user->getIsAdmin(), true);
    $this->assertTrue(password_verify($password, $user->getPassword()));
  }

  public function testMakeUser () {
    $email = faker\email();
    $password = faker\word();

    $id = seeder\makeUser($email, $password)->getId();
    $user = \UserQuery::create()->findPK($id);

    $this->assertTrue($user != null);
    $this->assertEquals($user->getEmail(), $email);
    $this->assertTrue(password_verify($password, $user->getPassword()));
  }

  // = FARMS ===

  public function testMakeFarm () {
    $data = faker\farmData();
    $owner = faker\makeUser();

    $id = seeder\makeFarm($owner, $data)->getId();
    $farm = \FarmQuery::create()->findPK($id);

    $this->assertTrue($farm != null);
    $this->assertEquals($farm->getName(), $data['name']);
    $this->assertEquals($farm->getAddress(), $data['address']);
    $this->assertEquals($farm->getWebsite(), $data['website']);
    $this->assertEquals($farm->getPhone(), $data['phone']);
    $this->assertEquals($farm->getEmail(), $data['email']);
    $this->assertEquals($farm->getuser()->getId(), $owner->getId());
  }

  // = PRODUCTS ===

  public function testMakeRootProduct () {
    $data = faker\productData();

    $id = seeder\makeRootProduct($data)->getId();
    $product = \ProductQuery::create()->findPK($id);

    $this->assertTrue($product != null);
    $this->assertTrue($product->isRoot());
    $this->assertEquals($product->getName(), $data['name']);
  }

  public function testMakeProduct () {
    $data = faker\productData();

    $id = seeder\makeProduct($data)->getId();
    $product = \ProductQuery::create()->findPK($id);

    $this->assertTrue($product != null);
    $this->assertEquals($product->getName(), $data['name']);
  }

  // = EVENTS ===

  public function testMakeEvent () {
    $data = faker\eventData();

    $id = seeder\makeEvent($data)->getId();
    $event = \EventQuery::create()->findPK($id);

    $this->assertTrue($event != null);
    $this->assertEquals($event->getTitle(), $data['title']);
    $this->assertEquals($event->getDescription(), $data['description']);
    // $this->assertEquals($event->getPublishAt('U'), $data['publishAt']);
    // $this->assertEquals($event->getBeginAt('U'), $data['beginAt']);
    // $this->assertEquals($event->getEndAt('U'), $data['endAt']);
  }

}
