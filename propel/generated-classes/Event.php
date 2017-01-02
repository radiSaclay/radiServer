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

  public function serialize () {
    return [
      "id" => $this->getId(),
      "farmId" => $this->getFarmId(),
      "productId" => $this->getProductId(),
      "description" => $this->getDescription(),
      "publishAt" => $this->getPublishAt(),
      "beginAt" => $this->getBeginAt(),
      "endAt" => $this->getEndAt(),
    ];
  }

  public function unserialize ($data) {
    if (isset($data["productId"])) $this->setProductId($data["productId"]);
    if (isset($data["description"])) $this->setDescription($data["description"]);
    if (isset($data["publishAt"])) $this->setPublishAt($data["publishAt"]);
    if (isset($data["beginAt"])) $this->setBeginAt($data["beginAt"]);
    if (isset($data["endAt"])) $this->setEndAt($data["endAt"]);
  }

}
