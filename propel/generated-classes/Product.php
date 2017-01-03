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

  public function serialize () {
    return [
      "id" => $this->getId(),
      "name" => $this->getName(),
      "parentId" => $this->getParentId()
    ];
  }

  public function unserialize ($data) {
    if (!isset($data["name"])){
     throw new Exception("Product needs a name, specify it in the json file");
   }
    if(
      ProductQuery::create()
      ->findByName($data["name"])
      ->count() > 0
    ){
      throw new Exception("There already exists a product with this name");
    }else{
      $this->setName($data["name"]);
    }
    if (isset($data["parentId"])) $this->setParent($data["parentId"]);
  }

  public function setParent ($parentId) {
    if(ProductQuery::create()
    ->findPK($parentId)){
      $this->setParentId($parentId);
    }else{
      throw new Exception("Parent does not exist");
    }
  }

}
