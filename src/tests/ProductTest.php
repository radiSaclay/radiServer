<?php
require_once '../../vendor/autoload.php';
require_once '../../propel/generated-conf/config.php';
require_once 'ProductHelper.php';

class ProductTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var \PHPUnit_Framework_MockObject_MockObject|ProductHelper
   */
  protected $productHelper;
  protected function setUp()
  {
    parent::setUp();
    $this->productHelper = new ProductHelper();
  }


  public function testProdLifeCycle(){
    // Create product
    $prodId = $this->productHelper->CreateProduct('TESTPROD');
    $this->productHelper->UpdateProduct($prodId, 'NEW Mis à Jour Name');
    $this->productHelper->DeleteProduct($prodId);
  }



  protected function tearDown()
  {
    parent::tearDown();
  }


}
