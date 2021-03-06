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

  // returns the number of subscribers of the farm
  public function countSubscribers () {
    return SubscriptionQuery::create()
      ->filterBySubscriptionId($this->getId())
      ->filterBySubscriptionType('farm')
      ->count();
  }
  // returns a boolean, true if the farm has more than one subscriber, 0 otherwise
  public function hasSubscriber ($user) {
    return SubscriptionQuery::create()
      ->filterByUserId($user->getId())
      ->filterBySubscriptionId($this->getId())
      ->filterBySubscriptionType('farm')
      ->count() > 0;
  }

  // adds the user (input) as subscriber to this farm
  public function addSubscriber ($user) {
    if (!$this->hasSubscriber($user)) {
      $sub = new Subscription();
      $sub->setUserId($user->getId());
      $sub->setSubscriptionId($this->getId());
      $sub->setSubscriptionType('farm');
      $sub->save();
    }
  }

  // removes the given user as subscriber of this farm
  public function removeSubscriber ($user) {
    return SubscriptionQuery::create()
      ->filterByUserId($user->getId())
      ->filterBySubscriptionId($this->getId())
      ->filterBySubscriptionType('farm')
      ->delete();
  }

  // > CRUD API

  // From Object to JSON
  // Level = level of detail of the object itself
  // embedded_level = level of detail of the objects contained inside him
  public function serialize ($level = 1, $embedded_level = -1, $request = null) {
    $farm = [];
    // Level 0
    $farm["id"] = $this->getId();
    $farm["name"] = $this->getName();
    // Level 1
    if ($level >= 1) {
      $farm["website"] = $this->getWebsite();
      $farm["address"] = $this->getAddress();
      $farm["phone"] = $this->getPhone();
      $farm["email"] = $this->getEmail();
      if ($request && auth\isUser($request)) {
        $farm["subscribed"] = $this->hasSubscriber(auth\getUser($request));
      }
      // Embedded
      if ($embedded_level < 0) {
        $farm["ownerId"] = $this->getUser()->getId();
        $farm["products"] = \collection\getIds($this->getProducts());
      } else {
        $farm["owner"] = $this->getUser()->serialize($embedded_level);
        $farm["products"] = \collection\serialize($this->getProducts(), $embedded_level);
      }
    }
    return $farm;
  }

  // JSON to Object
  public function unserialize ($data) {
    if (isset($data["name"])) $this->setName($data["name"]);
    if (isset($data["address"])) $this->setAddress($data["address"]);
    if (isset($data["website"])) $this->setWebsite($data["website"]);
    if (isset($data["phone"])) $this->setPhone($data["phone"]);
    if (isset($data["email"])) $this->setEmail($data["email"]);
  }

}
