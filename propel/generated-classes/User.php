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

  // Checks if the email is a valid one
  function valid_email($email) {
    return !!filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  // Returns true if the user has a valid email and password, false otherwise
  function has_mail_n_pass(){
    if( !$this->valid_email($this->getEmail()) && !is_null($this->getPassword()) ){
      return true;
    }else{
      return false;
    }
  }

  // Returns true if the user has a no email and password, false otherwise
  function has_no_email_or_pass(){
    if ( is_null($this->getEmail()) && is_null($this->getPassword()) ){
      return true;
    }else {
      return false;
    }
  }

  // > CRUD API
  // This code is executed just before inserting a new user on the DB
  // and checks if the user has a regular account (email + password) or facebook account
  // but he can't have both at the same time for the same account
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

  // From Object to JSON
  // Level = level of detail of the object itself
  // embedded_level = level of detail of the objects contained inside him
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
