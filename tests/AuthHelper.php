<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../propel/generated-conf/config.php';
require_once __DIR__ . '/bootstrap.php';

class AuthHelper
{

  public function CreateAccount($email, $password){
    $response = makeRequest('POST', '/auth/signup', [
      'email' => $email,
      'password' => $password
    ]);
    $response_arr = json_decode($response->getBody(), true);
    echo var_dump($response_arr);
    echo $response->getStatusCode();
    PHPUnit_Framework_Assert::assertEquals(true, $response_arr['validated']);
    PHPUnit_Framework_Assert::assertArrayHasKey('token', $response_arr);
    $this->Login($email, $password);
    return $response_arr['token'];
  }

  public function Login($email, $password, $shouldExist = true){
    $response = makeRequest('POST', '/auth/login', [
      'email' => $email,
      'password' => $password
    ]);
    $response_arr = json_decode($response->getBody(), true);
    if ($shouldExist) {
      PHPUnit_Framework_Assert::assertEquals(true, $response_arr['validated']);
      PHPUnit_Framework_Assert::assertArrayHasKey('token', $response_arr);
      return $response_arr['token'];
    }else{
      PHPUnit_Framework_Assert::assertEquals(false, $response_arr['validated']);
      PHPUnit_Framework_Assert::assertEquals('Wrong credentials', $response_arr['msg']);
      return null;
    }
  }


  public function DeleteAccount($token){
    $response = makeRequest('DELETE', '/auth/delete',[
        'Authorization' => $token
      ]);
    PHPUnit_Framework_Assert::assertEquals(200 ,$response->getStatusCode());
  }

  public function GetAccount($token){
    $response = makeRequest('GET', '/auth/user',[

        'Authorization' => $token
      ]);
    $response_arr = json_decode($response->getBody(), true);
    PHPUnit_Framework_Assert::assertEquals(200 ,$response->getStatusCode());
  }



}
