<?php

// Get query objects
function query ($table) {
  if ($table == "pin") return PinQuery::create();
  if ($table == "user") return UserQuery::create();
  if ($table == "farms") return FarmQuery::create();
  if ($table == "order") return OrderQuery::create();
  if ($table == "events") return EventQuery::create();
  if ($table == "products") return ProductQuery::create();
  if ($table == "farm-product") return FarmProductQuery::create();
  if ($table == "subscription") return SubscriptionQuery::create();
}

// Get instance objects
function instanciate ($table) {
  if ($table == "pin") return new Pin();
  if ($table == "user") return new User();
  if ($table == "farms") return new Farm();
  if ($table == "order") return new Order();
  if ($table == "events") return new Event();
  if ($table == "products") return new Product();
  if ($table == "farm-product") return new FarmProduct();
  if ($table == "subscription") return new Subscription();
}
