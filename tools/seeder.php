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

// = FARMS ===

function makeFarm ($owner, $data = []) {
  $farm = new \Farm();
  $farm->setUser($owner);
  $farm->unserialize($data);
  $farm->save();
  return $farm;
}

// = PRODUCTS ===

function makeRootProduct ($data = []) {
  $root = \ProductQuery::create()->findRoot();
  if ($root == null) {
    $root = new \Product();
    $root->makeRoot();
  }
  $root->unserialize($data);
  $root->save();
  return $root;
}

function makeProduct ($data = []) {
  $product = new \Product();
  $product->unserialize($data);
  $product->save();
  return $product;
}

// = EVENTS ===

function makeEvent ($data = []) {
  $event = new \Event();
  $event->unserialize($data);
  $event->save();
  return $event;
}
