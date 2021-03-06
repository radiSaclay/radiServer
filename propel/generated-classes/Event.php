<?php

use Base\Event as BaseEvent;

/**
 * Skeleton subclass for representing a row from the 'event' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Event extends BaseEvent {

  // returns a boolean: true if the event has a given product, false otherwise
  public function hasProduct ($product) {
    return $this->getProducts()->contains($product);
  }

  // From Object to JSON
  // Level = level of detail of the object itself
  // embedded_level = level of detail of the objects contained inside him
  public function serialize ($level = 1, $embedded_level = -1, $request = null) {
    $event = [];
    // Level 0
    $event["id"] = $this->getId();
    $event["title"] = $this->getTitle();
    // Level 1
    if ($level >= 1) {
      $event["description"] = $this->getDescription();
      $event["publishAt"] = $this->getPublishAt('U');
      $event["beginAt"] = $this->getBeginAt('U');
      $event["endAt"] = $this->getEndAt('U');
      $farm["pins"] = $this->countUsers();
      if ($request && auth\isUser($request)) {
        $event["pinned"] = $this->getUsers()->contains(auth\getUser($request));
      }
      // Embedded
      $farm = $this->getFarm();
      if ($embedded_level < 0) {
        $event["farmId"] = $farm ? $farm->getId() : null;
        $event["products"] = \collection\getIds($this->getProducts());
      } else {
        $event["farm"] = $farm ? $farm->serialize($embedded_level) : null;
        $event["products"] = \collection\serialize($this->getProducts(), $embedded_level);
      }
    }
    return $event;
  }

  // From JSON to object
  public function unserialize ($data) {
    if (isset($data["title"])) $this->setTitle($data["title"]);
    if (isset($data["description"])) $this->setDescription($data["description"]);
    if (isset($data["publishAt"])) $this->setPublishAt(new DateTime('@'.$data["publishAt"]));
    if (isset($data["beginAt"])) $this->setBeginAt(new DateTime('@'.$data["beginAt"]));
    if (isset($data["endAt"])) $this->setEndAt(new DateTime('@'.$data["endAt"]));
    if (isset($data["products"])) $this->resetProducts($data["products"]);
  }

  // Removes all products from the event and adds the products with id corresponding to the array $productIds
  public function resetProducts ($productIds) {
    foreach ($this->getProducts() as $product) {
      $this->removeProduct($product);
    }
    foreach ($productIds as $id) {
      $this->addProduct(ProductQuery::create()->findPk($id));
    }
  }

}
