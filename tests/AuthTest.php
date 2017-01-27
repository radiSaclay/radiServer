<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../propel/generated-conf/config.php';
require_once __DIR__ . '/AuthHelper.php';


class authTest extends ServerTestCase
{
  protected $email = 'TESTMAI33L@gmail.com';
  protected $password = 'TESTPASS';
  /**
   * @var \PHPUnit_Framework_MockObject_MockObject|AuthHelper
   */
  protected $authHelper;
  protected function setUp()
  {
    parent::setUp();
    $this->authHelper = new AuthHelper();
  }
  public function testAccountLifeCycle(){
    $token = $this->authHelper->CreateAccount($this->email, $this->password);
    $this->authHelper->GetAccount($token);
    $this->authHelper->DeleteAccount($token);
    $this->authHelper->Login($this->email, $this->password, false);
//    try {
//      $this->authHelper->GetAccount($token);
//    }catch (GuzzleHttp\Exception\ClientException $e){
//      self::assertEquals(404, $e->getResponse()->getStatusCode());
//    }
  }
}
