<?php
require_once '../../vendor/autoload.php';
require_once '../../propel/generated-conf/config.php';
use GuzzleHttp\Client;

class ProductTest extends PHPUnit_Framework_TestCase
{
  protected $client;
  protected $email = 'admin';
  protected $password = 'admin';
  protected $token;
  protected $productId;
  protected function setUp()
  {
    parent::setUp();
    $this->token = $this->LoginAdmin();

    // Create a client to talk to the server
    $this->client = new Client([
      // Base URI is used with relative requests
      'base_uri' => 'http://127.0.0.1/api/products/',
      // You can set any number of default request options.
      'timeout'  => 2.0,
    ]);

  }

  public function testProdLifeCicle(){
    $prodId = $this->CreateProduct('TESTPROD');
    $this->UpdateProduct($prodId, 'Prod Mis à Jour');
    $this->GetProduct($prodId, 'Prod Mis à Jour');
    $this->DeleteProduct($prodId);
    try {
      $this->GetProduct($prodId, 'Prod Mis à Jour');
    }catch (GuzzleHttp\Exception\ClientException $e){
      self::assertEquals(404, $e->getResponse()->getStatusCode());
    }
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
    self::assertEquals(true, $response_arr['validated']);
    self::assertArrayHasKey('token', $response_arr);
    return $response_arr['token'];
  }

  public function CreateProduct($name){
    $response = $this->client->request('POST', '', [
      'headers' => [
      'Authorization' => $this->token
    ],
      'json' => [
      'name' => $name
    ]]);
    $response_arr = json_decode($response->getBody(), true);
    self::assertArrayHasKey('id', $response_arr);
    self::assertEquals($name, $response_arr['name']);
    return $response_arr['id'];
  }


  public function UpdateProduct($originalId, $newName){
    $response = $this->client->request('PUT', '' . $originalId, [
      'headers' => [
        'Authorization' => $this->token
      ],
      'json' => [
        'name' => $newName
      ]]);
    $response_arr = json_decode($response->getBody(), true);
    self::assertArrayHasKey('id', $response_arr);
    self::assertEquals($newName, $response_arr['name']);
    return $response_arr['id'];
  }



  public function GetProduct($id, $expectedName){
    $response = $this->client->request('GET', '' . $id);
    $response_arr = json_decode($response->getBody(), true);
    self::assertArrayHasKey('id', $response_arr);
    self::assertEquals($expectedName, $response_arr['name']);
    return $response_arr['id'];
  }


  public function DeleteProduct($id){
    $response = $this->client->request('DELETE', '' . $id, [
      'headers' => [
        'Authorization' => $this->token
      ]]);
    self::assertEquals(200, $response->getStatusCode());
  }

  protected function tearDown()
  {
    parent::tearDown();
  }


}
