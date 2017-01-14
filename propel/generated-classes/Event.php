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

  public function serialize ($level = 1, $embedded_level = -1) {
    $event = [];
    // Level 0
    $event["id"] = $this->getId();
    $event["title"] = $this->getTitle();
    // Level 1
    if ($level >= 1) {
      $event["description"] = $this->getDescription();
      $event["publishAt"] = $this->getPublishAt();
      $event["BeginAt"] = $this->getBeginAt();
      $event["EndAt"] = $this->getEndAt();
      // Embedded
      if ($embedded_level < 0) {
        $event["farmId"] = $this->getFarm()->getId();
        $event["products"] = \collection\getIds($this->getProducts());
      } else {
        $event["farm"] = $this->getFarm()->serialize($embedded_level);
        $event["products"] = \collection\serialize($this->getProducts(), $embedded_level);
      }
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
