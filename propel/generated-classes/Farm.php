<?php

use Base\Farm as BaseFarm;

/**
 * Skeleton subclass for representing a row from the 'farm' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Farm extends BaseFarm {

  public function getSubscribers () {
    return SubscriptionQuery::create()
      ->filterBySubscriptionId($this->getId())
      ->filterBySubscriptionType('farm')
      ->find();
  }

  public function serialize () {
    return [
      "id" => $this->getId(),
      "name" => $this->getName(),
      "ownerId" => $this->getOwnerId(),
      "address" => $this->getAddress(),
      "website" => $this->getWebsite(),
      "phone" => $this->getPhone(),
      "email" => $this->getEmail(),
    ];
  }

  public function unserialize ($data) {
    if (isset($data["name"])) $this->setName($data["name"]);
    if (isset($data["address"])) $this->setAddress($data["address"]);
    if (isset($data["website"])) $this->setWebsite($data["website"]);
    if (isset($data["phone"])) $this->setPhone($data["phone"]);
    if (isset($data["phone"])) $this->setEmail($data["email"]);
  }

}
