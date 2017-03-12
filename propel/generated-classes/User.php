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

  function valid_email($email) {
    return !!filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  function has_mail_n_pass(){
    if( !$this->valid_email($this->getEmail()) && !is_null($this->getPassword()) ){
      return true;
    }else{
      return false;
    }
  }

  function has_no_email_or_pass(){
    if ( is_null($this->getEmail()) && is_null($this->getPassword()) ){
      return true;
    }else {
      return false;
    }
  }

  // > CRUD API
  public function preInsert(\Propel\Runtime\Connection\ConnectionInterface $con = null)
  {
    if(!$this->has_no_email_or_pass()){
      if(!is_null($this->getFbId())){
        throw new Exception("Has both (email or pass) and facebook id.");
      }
    }
    if ($this->has_no_email_or_pass()) {
      if(is_null($this->getFbId())){
        throw new Exception("Has neither (email and pass) nor facebook id.");
      }
    }
    return true;
  }

  public function serialize ($level = 1, $embedded_level = -1, $request = null) {
    $user = [];
    // Level 0
    $user["id"] = $this->getId();
    $user["email"] = $this->getEmail();
    // Level 1
    if ($level >= 1) {
      $user["isAdmin"] = $this->getIsAdmin();
    }

    return $user;
  }
}
