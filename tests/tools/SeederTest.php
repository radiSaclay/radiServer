<?php

use PHPUnit\Framework\TestCase;
require_once 'tools/seeder.php';

final class SeederTest extends ServerTestCase {

  // = USERS ===

  public function testMakeAdmin () {
    $faker = Faker\Factory::create();
    $email = $faker->email;
    $password = $faker->word;

    $id = seeder\makeAdmin($email, $password)->getId();
    $user = \UserQuery::create()->findPK($id);

    $this->assertTrue($user != null);
    $this->assertEquals($user->getEmail(), $email);
    $this->assertEquals($user->getIsAdmin(), true);
    $this->assertTrue(password_verify($password, $user->getPassword()));
  }

  public function testMakeUser () {
    $faker = Faker\Factory::create();
    $email = $faker->email;
    $password = $faker->word;

    $id = seeder\makeUser($email, $password)->getId();
    $user = \UserQuery::create()->findPK($id);

    $this->assertTrue($user != null);
    $this->assertEquals($user->getEmail(), $email);
    $this->assertTrue(password_verify($password, $user->getPassword()));
  }

  // = FARMS ===

  public function testMakeFarm () {
    $faker = Faker\Factory::create();
    $ownerEmail = $faker->email;
    $ownerPassword = $faker->word;
    $name = $faker->word;
    $data = [
      'address' => $faker->address,
      'website' => $faker->url,
      'phone' => $faker->e164PhoneNumber,
      'email' => $faker->email,
    ];

    $owner = seeder\makeUser($ownerEmail, $ownerPassword);
    $id = seeder\makeFarm($owner, $name, $data)->getId();
    $farm = \FarmQuery::create()->findPK($id);

    $this->assertTrue($farm != null);
    $this->assertEquals($farm->getName(), $name);
    $this->assertEquals($farm->getAddress(), $data['address']);
    $this->assertEquals($farm->getWebsite(), $data['website']);
    $this->assertEquals($farm->getPhone(), $data['phone']);
    $this->assertEquals($farm->getEmail(), $data['email']);
    $this->assertEquals($farm->getuser()->getId(), $owner->getId());
  }

  // = PRODUCTS ===

  public function testMakeRootProduct () {
    $faker = Faker\Factory::create();
    $name = $faker->word;

    $id = seeder\makeRootProduct($name)->getId();
    $product = \ProductQuery::create()->findPK($id);

    $this->assertTrue($product != null);
    $this->assertTrue($product->isRoot());
    $this->assertEquals($product->getName(), $name);
  }

  public function testMakeProduct () {
    $faker = Faker\Factory::create();
    $name = $faker->word;

    $id = seeder\makeProduct($name)->getId();
    $product = \ProductQuery::create()->findPK($id);

    $this->assertTrue($product != null);
    $this->assertEquals($product->getName(), $name);
  }

  // = EVENTS ===

  public function testMakeEvent () {
    $faker = Faker\Factory::create();
    $title = $faker->sentence;
    $content = $faker->paragraph;

    $id = seeder\makeEvent ($title, $content)->getId();
    $event = \EventQuery::create()->findPK($id);

    $this->assertTrue($event != null);
    $this->assertEquals($event->getTitle(), $title);
    $this->assertEquals($event->getDescription(), $content);
  }

}
