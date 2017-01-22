<?php
require_once '../../vendor/autoload.php';
require_once '../../propel/generated-conf/config.php';
use GuzzleHttp\Client;

class ProductHelper
{
  protected $client;
  protected $email = 'admin';
  protected $password = 'admin';
  protected $token;

  function __construct() {
    $this->token = $this->LoginAdmin();
    // Create a client to talk to the server
    $this->client = new Client([
      // Base URI is used with relative requests
      'base_uri' => 'http://127.0.0.1/api/products/',
      // You can set any number of default request options.
      'timeout'  => 2.0,
    ]);
  }


  public function LoginAdmin(){
    $this->client = new Client([
      // Base URI is used with relative requests
      'base_uri' => 'http://127.0.0.1/auth/',
      // You can set any number of default request options.
      'timeout'  => 2.0,
    ]);
    $response = $this->client->request('POST', 'login', ['json' => [
      'email' => $this->email,
      'password' => $this->password
    ]]);
    $response_arr = json_decode($response->getBody(), true);
    PHPUnit_Framework_Assert::assertEquals(true, $response_arr['validated']);
    PHPUnit_Framework_Assert::assertArrayHasKey('token', $response_arr);
    return $response_arr['token'];
  }
  // Creates an object with $name and returns its id
  public function CreateProduct($name, $parentId = null){
    // Send request to create product
    $response = $this->client->request('POST', '', [
      'headers' => [
        'Authorization' => $this->token
      ],
      'json' => [
        'name' => $name,
        'parentId' => $parentId
      ]]);
    $response_arr = json_decode($response->getBody(), true);
    // Make sure it has an id
    PHPUnit_Framework_Assert::assertArrayHasKey('id', $response_arr);
    // Make sure it has the correct name
    PHPUnit_Framework_Assert::assertEquals($name, $response_arr['name']);
    // Make independent request to verify the product creation
    $this->GetProduct($response_arr['id'], $name);
    return $response_arr['id'];
  }

  // Updates an object whose id is $originalId with new name $newName and returns its id
  public function UpdateProduct($originalId, $newName){
    // Send request
    $response = $this->client->request('PUT', '' . $originalId, [
      'headers' => [
        'Authorization' => $this->token
      ],
      'json' => [
        'name' => $newName
      ]]);
    // Get response
    $response_arr = json_decode($response->getBody(), true);
    // Make sure it still has the same id
    PHPUnit_Framework_Assert::assertEquals($originalId, $response_arr['id']);
    PHPUnit_Framework_Assert::assertEquals($newName, $response_arr['name']);
    return $response_arr['id'];
  }


  // Checks if the product with id $id has the name $expectedName
  public function GetProduct($id, $expectedName){
    $response = $this->client->request('GET', '' . $id);
    $response_arr = json_decode($response->getBody(), true);
    PHPUnit_Framework_Assert::assertArrayHasKey('id', $response_arr);
    PHPUnit_Framework_Assert::assertEquals($expectedName, $response_arr['name']);
    return $response_arr['id'];
  }


  public function DeleteProduct($id){
    $response = $this->client->request('DELETE', '' . $id, [
      'headers' => [
        'Authorization' => $this->token
      ]]);
    PHPUnit_Framework_Assert::assertEquals(200, $response->getStatusCode());
    // Make sure that when looking for the product deleted it gets a 404 error
    try {
      $this->GetProduct($id, 'Anything');
    }catch (GuzzleHttp\Exception\ClientException $e){
      PHPUnit_Framework_Assert::assertEquals(404, $e->getResponse()->getStatusCode());
    }
  }




}
