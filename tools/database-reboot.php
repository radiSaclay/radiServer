<?php

// Load dependecies
require_once 'vendor/autoload.php';
require_once 'propel/generated-conf/config.php';

// Empty all
EventProductQuery::create()->deleteAll();
FarmProductQuery::create()->deleteAll();
SubscriptionQuery::create()->deleteAll();
PinQuery::create()->deleteAll();
ProductQuery::create()->deleteAll();
FarmQuery::create()->deleteAll();
EventQuery::create()->deleteAll();
UserQuery::create()->deleteAll();
// OrderQuery::create()->deleteAll();

// Create admin
$admin = new User();
$admin->setEmail('admin');
$admin->setPassword(password_hash('admin', PASSWORD_DEFAULT));
$admin->setIsAdmin(true);
$admin->save();

// Create all product
$allProducts = new Product();
$allProducts->setName('tout');
$allProducts->makeRoot();
$allProducts->save();

echo 'done';
