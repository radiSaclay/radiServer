<?php

use PHPUnit\Framework\TestCase;
require_once 'tools/seeder.php';

final class SeederTest extends TestCase {

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

  public function testMakeFarmer () {
    $faker = Faker\Factory::create();
    $email = $faker->email;
    $password = $faker->word;
    $farmName = $faker->word;
    $farmData = [
      'address' => $faker->address,
      'website' => $faker->url,
      'phone' => $faker->e164PhoneNumber,
      'email' => $faker->email,
    ];

    $id = seeder\makeFarmer($email, $password, $farmName, $farmData)->getId();
    $user = \UserQuery::create()->findPK($id);

    $this->assertTrue($user != null);
    $this->assertEquals($user->getEmail(), $email);
    $this->assertTrue(password_verify($password, $user->getPassword()));

    // $farm = $user->getFarms()->getFirst();
    // $this->assertTrue($farm != null);
    // $this->assertEquals($farm->getName(), $farmName);
    // $this->assertEquals($farm->getAddress(), $farmData['address']);
    // $this->assertEquals($farm->getWebsite(), $farmData['website']);
    // $this->assertEquals($farm->getPhone(), $farmData['phone']);
    // $this->assertEquals($farm->getEmail(), $farmData['email']);
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

  // public function testMakeRootProduct () {
  //   $faker = Faker\Factory::create();
  //   $name = $faker->word;
  //
  //   $id = seeder\makeRootProduct($name)->getId();
  //   $product = \ProductQuery::create()->findPK($id);
  //
  //   $this->assertTrue($product != null);
  //   $this->assertTrue($product->isRoot());
  //   $this->assertEquals($product->getName(), $name);
  // }

  // public function testMakeProduct () {
  //   $faker = Faker\Factory::create();
  //   $name = $faker->word;
  //
  //   $id = seeder\makeRootProduct($name)->getId();
  //   $product = \ProductQuery::create()->findPK($id);
  //
  //   $this->assertTrue($product != null);
  //   $this->assertEquals($product->getName(), $name);
  // }

  // = EVENTS ===

  // public function testMakeEvent ($title, $content, $farm = null, $products = []) {
  //   $event = new \Event();
  //   $event->setTitle($title);
  //   $event->setDescription($content);
  //   if ($farm) $event->setFarm($farm);
  //   foreach ($products as $product)
  //     $event->addProduct($product);
  //   $event->save();
  //   return $event;
  // }

}
