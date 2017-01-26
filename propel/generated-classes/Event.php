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

    public function serialize ($level = 1, $embedded_level = -1, $request = null) {
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

  public function unserialize ($data) {
    if (isset($data["productId"])) $this->setProductId($data["productId"]);
    if (isset($data["name"])) $this->setDescription($data["name"]);
    if (isset($data["description"])) $this->setDescription($data["description"]);
    if (isset($data["publishAt"])) $this->setPublishAt($data["publishAt"]);
    if (isset($data["beginAt"])) $this->setBeginAt($data["beginAt"]);
    if (isset($data["endAt"])) $this->setEndAt($data["endAt"]);
  }

}
