<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../propel/generated-conf/config.php';
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


  // Checks if the product with id $id has the name $expectedName and if its children are
  // as passed in $children which should respect the format ['id'=>0, 'name'='orange']
  public function GetProduct($id, $expectedName, array $children = null, array $ancestors = null){
    // Get the product
    $response = $this->client->request('GET', '' . $id);
    // Get response as array
    $response_arr = json_decode($response->getBody(), true);
    // Make sure it has the same id that was requested
    PHPUnit_Framework_Assert::assertEquals($response_arr['id'], $id);
    // And expected name
    PHPUnit_Framework_Assert::assertEquals($expectedName, $response_arr['name']);
    // Check children
    if($children){
      PHPUnit_Framework_Assert::assertEquals(count($children), count($response_arr['children']));
      foreach($children as $child){
        PHPUnit_Framework_Assert::assertTrue($this->hasChild($response_arr['children'],$child['name'], $child['id']));
      }
    }
    // Check ancestors
    if($ancestors){
      PHPUnit_Framework_Assert::assertEquals(count($ancestors), count($response_arr['ancestors']));
      foreach($ancestors as $ancestor){
        PHPUnit_Framework_Assert::assertTrue($this->hasAncestors($response_arr['ancestors'],$ancestor['name'], $ancestor['id']));
      }
    }
    return $response_arr['id'];
  }

  // Returns true if inside the array $children (which needs to follow format ['id'=>0, 'name'='orange'])
  // there is an array 'name' = $childName and 'id' = $childId. False otherwise.
  public function hasChild(array $children, $childName, $childId){
    foreach($children as $child){
      if(($child['id'] == $childId) && ($child['name'] == $childName)){
        return true;
      }
    }
    return false;
  }

  // Returns true if inside the array $ancestors (which needs to follow format ['id'=>0, 'name'='orange'])
  // there is an array 'name' = $ancestorName and 'id' = $ancestorsId. False otherwise.
  public function hasAncestors(array $ancestors, $ancestorName, $ancestorsId){
    foreach($ancestors as $ancestor){
      if(($ancestor['id'] == $ancestorsId) && ($ancestor['name'] == $ancestorName)){
        return true;
      }
    }
    return false;
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
