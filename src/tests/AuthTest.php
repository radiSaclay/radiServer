<?php
require_once '../../vendor/autoload.php';
require_once '../../propel/generated-conf/config.php';
use GuzzleHttp\Client;

class authTest extends PHPUnit_Framework_TestCase
{
  protected $client;
  protected $email = 'TESTMAIL@gmail.com';
  protected $password = 'TESTPASS';
  protected function setUp()
  {
    parent::setUp();
    // Create a client to talk to the server
    $this->client = new Client([
      // Base URI is used with relative requests
      'base_uri' => 'http://127.0.0.1/auth/',
      // You can set any number of default request options.
      'timeout'  => 2.0,
    ]);
  }

  public function testCreateAccount(){
    $response = $this->client->request('POST', 'signup', ['json' => [
      'email' => $this->email,
      'password' => $this->password
    ]]);
    $response_arr = json_decode($response->getBody(), true);
    self::assertEquals(true, $response_arr['validated']);
    self::assertArrayHasKey('token', $response_arr);
    return $response_arr['token'];
  }

  /**
   * @depends testCreateAccount
   */
  public function testLogin(){
    $response = $this->client->request('POST', 'login', ['json' => [
      'email' => $this->email,
      'password' => $this->password
    ]]);
    $response_arr = json_decode($response->getBody(), true);
    self::assertEquals(true, $response_arr['validated']);
    self::assertArrayHasKey('token', $response_arr);
    return $response_arr['token'];
  }

  /**
   * @depends testLogin
   */
  public function testDeleteAccount($token){
    $response = $this->client->request('DELETE', 'delete',[
      'headers' => [
        'Authorization' => $token
      ]]);
    echo $response->getBody();
  }



}
