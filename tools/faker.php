<?php namespace faker;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/seeder.php';

// = USERS ===

function email () {
  return \Faker\Factory::create()->email;
}

function word () {
  return \Faker\Factory::create()->word;
}

function makeAdmin () {
  return \seeder\makeAdmin(email(), word());
}

function makeUser () {
  return \seeder\makeUser(email(), word());
}

// = FARMS ===

function farmData () {
  $faker = \Faker\Factory::create();
  return [
    'name' => $faker->word,
    'address' => $faker->address,
    'website' => $faker->url,
    'phone' => $faker->e164PhoneNumber,
    'email' => $faker->email,
  ];
}

function makeFarm ($owner) {
  return \seeder\makeFarm($owner, farmData());
}

// = PRODUCTS ===

function productData () {
  $faker = \Faker\Factory::create();
  return [
    'name' => $faker->word,
  ];
}

function makeRootProduct () {
  return \seeder\makeRootProduct(productData());
}

function makeProduct () {
  return \seeder\makeProduct(productData());
}

// = EVENTS ===

function eventData () {
  $faker = \Faker\Factory::create();
  return [
    'title' => $faker->word,
    'description' => $faker->sentence,
    // 'publishAt' => $faker->unixTime,
    // 'beginAt' => $faker->unixTime,
    // 'endAt' => $faker->unixTime,
  ];
}

function makeEvent ($farm = null) {
  return \seeder\makeEvent(eventData(), $farm);
}
