<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../propel/generated-conf/config.php';
require_once __DIR__ . '/ProductHelper.php';

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
    // Create "Applee" who is child of 'tout'
    $apple = $this->productHelper->CreateProduct('Applee', 1);
    // Create Apple Child who is child of Apple and Tout
    $apple_child = $this->productHelper->CreateProduct('Apple Child', $apple);
    // Create Apple Second Child who is child of Apple and Tout
    $apple_second_child = $this->productHelper->CreateProduct('Apple Second Child', $apple);
    // Create Apple Child Child who is child of Apple Child and Tout
    $apple_child_child = $this->productHelper->CreateProduct('Apple Child Child', $apple_child);
    // Create Orange who is child of 'tout'
    $orange = $this->productHelper->CreateProduct('Orange', 1);
    // Correct Apple name
    $this->productHelper->UpdateProduct($apple, 'Apple');
    // Check that Apple has two children and one ancestor and that they are what's expected
    $this->productHelper->GetProduct($apple, 'Apple', [
      ['id'=>$apple_child, 'name' =>'Apple Child'],
      ['id'=>$apple_second_child, 'name' =>'Apple Second Child']
    ],[['id'=>1, 'name' => 'tout']]);
    // Check that Apple Child has one child and two ancestors
    $this->productHelper->GetProduct($apple_child, 'Apple Child', [
      ['id'=>$apple_child_child, 'name' =>'Apple Child Child']
    ],
      [
        ['id'=>1, 'name' => 'tout'],
        ['id'=>$apple, 'name' =>'Apple']
      ]
      );

    // Deleting Apple second child and checking that all rest remains the same
    $this->productHelper->DeleteProduct($apple_second_child);

    // Check that Apple now has one child and one ancestor and that they are what's expected
    $this->productHelper->GetProduct($apple, 'Apple', [
      ['id'=>$apple_child, 'name' =>'Apple Child']
    ],[['id'=>1, 'name' => 'tout']]);
    // Check that Apple Child has one child and two ancestors
    $this->productHelper->GetProduct($apple_child, 'Apple Child', [
      ['id'=>$apple_child_child, 'name' =>'Apple Child Child']
    ],
      [
        ['id'=>1, 'name' => 'tout'],
        ['id'=>$apple, 'name' =>'Apple']
      ]
    );

    // Deleting Apple child and checking that all rest remains the same
    $this->productHelper->DeleteProduct($apple_child);

    // Check that Apple has now 0 children and one ancestor and that they are what's expected
    $this->productHelper->GetProduct($apple, 'Apple', null,[['id'=>1, 'name' => 'tout']]);

    $this->productHelper->DeleteProduct($apple);

  }



  protected function tearDown()
  {
    parent::tearDown();
  }


}
