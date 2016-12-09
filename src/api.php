<?php namespace api;

// ==================================================
// > Events
// ==================================================
function hasPinned ($user, $event) {
  return $event->getUsers()->contains($user);
}

function serializeEvent ($event, $user = null) {
  return [
    "id" => $event->getId(),
    "farmId" => $event->getFarmId(),
    "productId" => $event->getProductId(),
    "description" => $event->getDescription(),
    "publishAt" => $event->getPublishAt(),
    "beginAt" => $event->getBeginAt(),
    "endAt" => $event->getEndAt(),
    "pins" => $event->countUsers(),
    "pinned" => $user ? hasPinned($user, $event) : false,
  ];
}

function unserializeEvent ($event, $data) {
  if (isset($data["productId"])) $event->setProductId($data["productId"]);
  if (isset($data["description"])) $event->setDescription($data["description"]);
  if (isset($data["publishAt"])) $event->setPublishAt($data["publishAt"]);
  if (isset($data["beginAt"])) $event->setBeginAt($data["beginAt"]);
  if (isset($data["endAt"])) $event->setEndAt($data["endAt"]);
}

// ==================================================
// > Farmer
// ==================================================

function hasSubscribed ($user, $id, $type) {
  return \SubscriptionQuery::create()
    ->filterByUserId($user->getId())
    ->filterBySubscriptionId($id)
    ->filterBySubscriptionType($type)
    ->count() > 0;
}

function serializeFarm ($farm, $user = null) {
  return [
    "id" => $farm->getId(),
    "name" => $farm->getName(),
    "ownerId" => $farm->getOwnerId(),
    "address" => $farm->getAddress(),
    "website" => $farm->getWebsite(),
    "phone" => $farm->getPhone(),
    "email" => $farm->getEmail(),
    "subscribed" => $user ? hasSubscribed($user, $farm->getId(), 'farm') : false,
  ];
}

function unserializeFarm ($farm, $data) {
  if (isset($data["name"])) $farm->setName($data["name"]);
  if (isset($data["address"])) $farm->setAddress($data["address"]);
  if (isset($data["website"])) $farm->setWebsite($data["website"]);
  if (isset($data["phone"])) $farm->setPhone($data["phone"]);
  if (isset($data["phone"])) $farm->setEmail($data["email"]);
}

// ==================================================
// > Users
// ==================================================
function serializeUser ($user) {
  $farms = $user->getFarms();
  return [
    "id" => $user->getId(),
    "email" => $user->getEmail(),
    "farm" => count($farms) ? serializeFarm($farms->getFirst()) : null
  ];
}
