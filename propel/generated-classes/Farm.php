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

  // > Subscriber

  public function countSubscribers () {
    return SubscriptionQuery::create()
      ->filterBySubscriptionId($this->getId())
      ->filterBySubscriptionType('farm')
      ->count();
  }

  public function hasSubscriber ($user) {
    return SubscriptionQuery::create()
      ->filterByUserId($user->getId())
      ->filterBySubscriptionId($this->getId())
      ->filterBySubscriptionType('farm')
      ->count() > 0;
  }

  public function addSubscriber ($user) {
    if (!$this->hasSubscriber($user)) {
      $sub = new Subscription();
      $sub->setUserId($user->getId());
      $sub->setSubscriptionId($this->getId());
      $sub->setSubscriptionType('farm');
      $sub->save();
    }
  }

  public function removeSubscriber ($user) {
    return SubscriptionQuery::create()
      ->filterByUserId($user->getId())
      ->filterBySubscriptionId($this->getId())
      ->filterBySubscriptionType('farm')
      ->delete();
  }

  // > CRUD API

  public function serialize ($level = 1, $embed_level = -1) {
    // Level 0
    $item = [
      "id" => $this->getId(),
      "name" => $this->getName(),
    ];
    // Level 1
    if ($level >= 1) {
      $item["address"] = $this->getAddress();
      $item["website"] = $this->getWebsite();
      $item["phone"] = $this->getPhone();
      $item["email"] = $this->getEmail();
      // Embed
      if ($embed_level < 0) {
        $item["ownerId"] = $this->getOwnerId();
      } else {
        $item["ownerId"] = $this->getUser()->serialize($embed_level);
      }
    }

    return $item;
  }

  public function unserialize ($data) {
    if (isset($data["name"])) $this->setName($data["name"]);
    if (isset($data["address"])) $this->setAddress($data["address"]);
    if (isset($data["website"])) $this->setWebsite($data["website"]);
    if (isset($data["phone"])) $this->setPhone($data["phone"]);
    if (isset($data["phone"])) $this->setEmail($data["email"]);
  }

}
