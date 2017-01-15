<?php

use Base\Product as BaseProduct;

/**
* Skeleton subclass for representing a row from the 'product' table.
*
*
*
* You should add additional methods to this class to meet the
* application requirements.  This class will only be generated as
* long as it does not already exist in the output directory.
*
*/
class Product extends BaseProduct {

  // > Subscriber

  public function countSubscribers () {
    return SubscriptionQuery::create()
      ->filterBySubscriptionId($this->getId())
      ->filterBySubscriptionType('product')
      ->count();
  }

  public function hasSubscriber ($user) {
    return SubscriptionQuery::create()
      ->filterByUserId($user->getId())
      ->filterBySubscriptionId($this->getId())
      ->filterBySubscriptionType('product')
      ->count() > 0;
  }

  public function addSubscriber ($user) {
    if (!$this->hasSubscriber($user)) {
      $sub = new Subscription();
      $sub->setUserId($user->getId());
      $sub->setSubscriptionId($this->getId());
      $sub->setSubscriptionType('product');
      $sub->save();
    }
  }

  public function removeSubscriber ($user) {
    return SubscriptionQuery::create()
      ->filterByUserId($user->getId())
      ->filterBySubscriptionId($this->getId())
      ->filterBySubscriptionType('product')
      ->delete();
  }

  // > CRUD API

  // Return Object as array
    public function serialize ($level = 1, $embedded_level = -1, $request = null) {
    $product = [];
    // Level 0
    $product["id"] = $this->getId();
    $product["name"] = $this->getName();
    // Level 1
    if ($level >= 1) {
      if ($request && auth\isUser($request)) {
        $product["subscribed"] = $this->hasSubscriber(auth\getUser($request));
      }
      // Embedded
      if ($embedded_level < 0) {
        $product["farms"] = \collection\getIds($this->getFarms());
        if ($this->isInTree()) {
          $product["children"] = \collection\getIds($this->getChildren());
          $product["ancestors"] = \collection\getIds($this->getAncestors());
        }
      } else {
        $product["farms"] = \collection\serialize($this->getFarms(), $embedded_level);
        if ($this->isInTree()) {
          $product["children"] = \collection\serialize($this->getChildren(), $embedded_level);
          $product["ancestors"] = \collection\serialize($this->getAncestors(), $embedded_level);
        }
      }
    }
    return $product;
  }

  public function unserialize ($data) {
    if (isset($data["name"])) $this->setName($data["name"]);
    if (isset($data["parentId"])) $this->changeParent($data["parentId"]);
  }

  // I do this check manually because even though the DB will reject
  // Inserts having an invalid foreign key, it will send back a generic error
  // such as: Unable to execute INSERT statement [INSERT INTO product
  // (parent_id, name, id, created_at, updated_at) VALUES (:p0, :p1, :p2, :p3, :p4)]
  public function changeParent ($parentId = null) {
    $parent = ProductQuery::create()->findPK($parentId);
    if ($parent) {
      $this->insertAsLastChildOf($parent);
    } else {
      throw new Exception("Parent does not exist");
    }
  }

}
