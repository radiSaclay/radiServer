<?php namespace seeder;

require_once __DIR__ . '/../vendor/autoload.php';

// = USERS ===

function makeAdmin ($email, $password) {
  $admin = new \User();
  $admin->setEmail($email);
  $admin->setPassword(password_hash($password, PASSWORD_DEFAULT));
  $admin->setIsAdmin(true);
  $admin->save();
  return $admin;
}

function makeUser ($email, $password) {
  $user = new \User();
  $user->setEmail($email);
  $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
  $user->save();
  return $user;
}

function makeFarmer ($email, $password, $farmName, $farmData = []) {
  $farmer = makeUser($email, $password);
  $farm = new \Farm($farmer, $farmName, $farmData);
  $farmer->save();
  return $farmer;
}

// = FARMS ===

function makeFarm ($owner, $name, $data = []) {
  $farm = new \Farm();
  $farm->setName($name);
  $farm->unserialize($data);
  $farm->setUser($owner);
  $farm->save();
  return $farm;
}

// = PRODUCTS ===

function makeRootProduct ($name) {
  $root = new \Product();
  $root->setName($name);
  $root->makeRoot();
  $root->save();
  return $root;
}

function makeProduct ($name, $parentId = null) {
  $root = new \Product();
  $root->setName($name);
  if ($parentId)
    $this->changeParent($parentId);
  $root->save();
  return $root;
}

// = EVENTS ===

function makeEvent ($title, $content, $farm = null, $products = []) {
  $event = new \Event();
  $event->setTitle($title);
  $event->setDescription($content);
  if ($farm) $event->setFarm($farm);
  foreach ($products as $product)
    $event->addProduct($product);
  $event->save();
  return $event;
}
