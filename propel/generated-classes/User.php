<?php

use Base\User as BaseUser;

/**
 * Skeleton subclass for representing a row from the 'user' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class User extends BaseUser {

    // > CRUD API

  public function serialize ($level = 1, $embed_level = -1) {
    // Level -1 Only Id
    if($level == -1){
      $user = [
        "id" => $this->getId()
      ];
      return $user;
    }
    // Level 0 Basic info, no children
    $user = [
      "id" => $this->getId(),
      "email" => $this->getEmail(),
    ];
    // Level 1, everything + children
    if ($level >= 1) {
      $user["isAdmin"] = $this->getIsAdmin();
    }

    return $user;
  }
}
