<?php
require_once '../../vendor/autoload.php';
require_once '../../propel/generated-conf/config.php';
use GuzzleHttp\Client;
class AuthHelper
{
  /**
   * @var \PHPUnit_Framework_MockObject_MockObject|Client
   */
  protected $client;
  function __construct()
  {
    // Create a client to talk to the server
    $this->client = new Client([
      // Base URI is used with relative requests
      'base_uri' => 'http://127.0.0.1/auth/',
      // You can set any number of default request options.
      'timeout'  => 2.0,
    ]);
  }

  public function CreateAccount($email, $password){
    $response = $this->client->request('POST', 'signup', ['json' => [
      'email' => $email,
      'password' => $password
    ]]);
    $response_arr = json_decode($response->getBody(), true);
    PHPUnit_Framework_Assert::assertEquals(true, $response_arr['validated']);
    PHPUnit_Framework_Assert::assertArrayHasKey('token', $response_arr);
    $this->Login($email, $password);
    return $response_arr['token'];
  }

  public function Login($email, $password, $shouldExist = true){
    $response = $this->client->request('POST', 'login', ['json' => [
      'email' => $email,
      'password' => $password
    ]]);
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
    $response = $this->client->request('DELETE', 'delete',[
      'headers' => [
        'Authorization' => $token
      ]]);
    PHPUnit_Framework_Assert::assertEquals(200 ,$response->getStatusCode());
  }

  public function GetAccount($token){
    $response = $this->client->request('GET', 'user',[
      'headers' => [
        'Authorization' => $token
      ]]);
    $response_arr = json_decode($response->getBody(), true);
    PHPUnit_Framework_Assert::assertEquals(200 ,$response->getStatusCode());
  }



}
