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
class Product extends BaseProduct
{
  // > CRUD API
  // Return Object as array
  public function serialize () {
    return [
      "id" => $this->getId(),
      "name" => $this->getName(),
      "parentId" => $this->getParentId()
    ];
  }

  public function unserialize ($data) {
    if (isset($data["name"])) $this->setName($data["name"]);
    if (isset($data["parentId"])) $this->setParent($data["parentId"]);
  }

// I do this check manually because even though the DB will reject
// Inserts having an invalid foreign key, it will send back a generic error
// such as: Unable to execute INSERT statement [INSERT INTO product
// (parent_id, name, id, created_at, updated_at) VALUES (:p0, :p1, :p2, :p3, :p4)]
  public function setParent ($parentId) {
    if(ProductQuery::create()
    ->findPK($parentId)){
      $this->setParentId($parentId);
    }else{
      throw new Exception("Parent does not exist");
    }
    $this->setParentId($parentId);
  }

}
