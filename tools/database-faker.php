<?php

// Load dependecies
require_once 'vendor/autoload.php';
require_once 'propel/generated-conf/config.php';

$faker = Faker\Factory::create();

require_once 'src/collection.php';

use Propel\Runtime\ActiveQuery\Criteria;

// Users
for ($i = 0; $i < 10; $i++) {
  $user = new User();
  $user->setEmail($faker->email);
  $user->setPassword(password_hash('secret', PASSWORD_DEFAULT));
  $user->save();
}

// Farms
for ($i = 0; $i < 3; $i++) {
  $owner = UserQuery::create()
    ->leftJoin('User.Farm')
    ->where('User.email <> "admin"')
    ->where('Farm.id is null')
    ->findOne();
  $farm = new Farm();
  $farm->setName($faker->word);
  $farm->setAddress($faker->address);
  $farm->setWebsite($faker->url);
  $farm->setPhone($faker->e164PhoneNumber );
  $farm->setEmail($faker->email);
  $farm->setUser($owner);
  $farm->save();
}

// Products
function makeProduct ($faker, $parent) {
  $farms = FarmQuery::create()
    ->orderByUpdatedAt('asc')
    ->limit(2)
    ->find();
  $product = new Product();
  $product->setName($faker->word);
  $product->insertAsLastChildOf($parent);
  foreach ($farms as $farm)
    $product->addFarm($farm);
  $product->save();
  return $product;
}
$root = ProductQuery::create()
  ->filterByName('tout')
  ->findOne();
for ($i = 0; $i < 4; $i++) {
  $parent = makeProduct($faker, $root);
  for ($j = 0; $j < 2; $j++)
    makeProduct($faker, $parent);
}

for ($i = 0; $i < 25; $i++) {
  $farm = FarmQuery::create()->orderByUpdatedAt('desc')->findOne();
  $products = ProductQuery::create()->orderByUpdatedAt('desc')->limit(3)->find();
  $event = new Event();
  $event->setTitle($faker->sentence);
  $event->setDescription($faker->paragraph);
  $event->setFarm($farm);
  foreach ($products as $product)
    $event->addProduct($product);
  $event->save();
}

echo 'done';
