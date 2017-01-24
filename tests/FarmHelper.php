<?php
require_once '../vendor/autoload.php';
require_once '../propel/generated-conf/config.php';
require_once 'AuthHelper.php';
use GuzzleHttp\Client;

class FarmHelper
{
  protected $client;
  protected $email = 'farmer';
  protected $password = 'farmer';
  protected $token;
  /**
   * @var \PHPUnit_Framework_MockObject_MockObject|AuthHelper
   */
  protected $authHelper;

  function __construct() {
    $this->authHelper = new AuthHelper();
    $this->authHelper->CreateAccount($this->email,$this->password);
    $this->token = $this->authHelper->Login($this->email,$this->password);
    // Create a client to talk to the server
    $this->client = new Client([
      // Base URI is used with relative requests
      'base_uri' => 'http://127.0.0.1/api/farms/',
      // You can set any number of default request options.
      'timeout'  => 2.0,
    ]);
  }

  public function LoginFarmer(){

  }

  public function CreateFarm($name, $address=null, $website=null, $phone=null, $email=null){
    $response = $this->client->request('POST', '', ['headers' => [
      'Authorization' => $this->token
    ],
      'json' => [
      'name' => $name,
      'address' => $address,
      'website' => $website,
      'phone' => $phone,
      'email' => $email
    ]
    ]);
    $response_arr = json_decode($response->getBody(), true);
    $this->CheckFarm($response_arr['id'], $name, $address, $website, $phone, $email);
    return $response_arr['id'];
  }

  public function UpdateFarm($id, $name, $address=null, $website=null, $phone=null, $email=null){
    $response = $this->client->request('PUT', $id . '', ['headers' => [
      'Authorization' => $this->token
    ],
      'json' => [
        'name' => $name,
        'address' => $address,
        'website' => $website,
        'phone' => $phone,
        'email' => $email
      ]
    ]);
    $response_arr = json_decode($response->getBody(), true);
    $this->CheckFarm($response_arr['id'], $name, $address, $website, $phone, $email);
    return $response_arr['id'];
  }

  public function CheckFarm($id, $name, $address = null, $website =null, $phone =null, $email=null){
    $response = $this->client->request('GET', '' . $id, ['headers' => [
      'Authorization' => $this->token
    ]]);
    $response_arr = json_decode($response->getBody(), true);
    PHPUnit_Framework_Assert::assertEquals($id,$response_arr['id']);
    PHPUnit_Framework_Assert::assertEquals($name,$response_arr['name']);
    PHPUnit_Framework_Assert::assertEquals($address,$response_arr['address']);
    PHPUnit_Framework_Assert::assertEquals($website,$response_arr['website']);
    PHPUnit_Framework_Assert::assertEquals($phone,$response_arr['phone']);
    PHPUnit_Framework_Assert::assertEquals($email,$response_arr['email']);
  }

  public function DeleteFarm($id){
    $response = $this->client->request('DELETE', '' . $id, ['headers' => [
      'Authorization' => $this->token
    ]]);
    PHPUnit_Framework_Assert::assertEquals(200, $response->getStatusCode());
    try {
      $this->CheckFarm($id, 'Anything');
    }catch (GuzzleHttp\Exception\ClientException $e){
      PHPUnit_Framework_Assert::assertEquals(404, $e->getResponse()->getStatusCode());
    }
  }





}
