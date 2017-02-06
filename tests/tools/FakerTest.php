<?php

use PHPUnit\Framework\TestCase;
require_once 'tools/faker.php';

final class FakerTest extends ServerTestCase {

  // = USERS ===

  public function testMakeAdmin () {
    $id = faker\makeAdmin()->getId();
    $user = \UserQuery::create()->findPK($id);
    $this->assertTrue($user != null);
    $this->assertEquals($user->getIsAdmin(), true);
  }

  public function testMakeUser () {
    $id = faker\makeUser()->getId();
    $user = \UserQuery::create()->findPK($id);
    $this->assertTrue($user != null);
  }

  // = FARMS ===

  public function testMakeFarm () {
    $owner = faker\makeUser();
    $id = faker\makeFarm($owner)->getId();
    $farm = \FarmQuery::create()->findPK($id);
    $this->assertTrue($farm != null);
    $this->assertEquals($farm->getuser()->getId(), $owner->getId());
  }

  // = PRODUCTS ===

  public function testMakeRootProduct () {
    $id = faker\makeRootProduct()->getId();
    $product = \ProductQuery::create()->findPK($id);
    $this->assertTrue($product != null);
    $this->assertTrue($product->isRoot());
  }

  public function testMakeProduct () {
    $id = faker\makeProduct()->getId();
    $product = \ProductQuery::create()->findPK($id);
    $this->assertTrue($product != null);
  }

  // = EVENTS ===

  public function testMakeEvent () {
    $id = faker\makeEvent()->getId();
    $event = \EventQuery::create()->findPK($id);
    $this->assertTrue($event != null);
  }

}
