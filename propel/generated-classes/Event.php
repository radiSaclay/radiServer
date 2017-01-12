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

  public function serialize ($level = 1, $embed_level = -1) {
    // Level 0
    $event = [
      "id" => $this->getId(),
      "name" => $this->getName()
    ];
    if ($level > 0){
      // Level 1
      $products = $this->getProducts();
      $event["farmId"] = $this->getFarm()->serialize($embed_level);
      $event["productId"] = \api\mapCollectionNoResponse($this->getProducts(), function ($prod) use ($embed_level) {
        $data = $prod->serialize($embed_level);
        return $data;
      });
      $event["description"] = $this->getDescription();
      $event["publishAt"] = $this->getPublishAt();
      $event["beginAt"] = $this->getBeginAt();
      $event["endAt"] = $this->getEndAt();


    }
    return $event;
  }

  public function unserialize ($data) {
    if (isset($data["productId"])) $this->setProductId($data["productId"]);
    if (isset($data["name"])) $this->setDescription($data["name"]);
    if (isset($data["description"])) $this->setDescription($data["description"]);
    if (isset($data["publishAt"])) $this->setPublishAt($data["publishAt"]);
    if (isset($data["beginAt"])) $this->setBeginAt($data["beginAt"]);
    if (isset($data["endAt"])) $this->setEndAt($data["endAt"]);
  }

}
