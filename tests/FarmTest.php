<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../propel/generated-conf/config.php';
require_once __DIR__ . '/FarmHelper.php';
/**
 * Created by PhpStorm.
 * User: tiberio
 * Date: 23/01/2017
 * Time: 02:54
 */
class FarmTest extends PHPUnit_Framework_TestCase
{
  /**
   * @var \PHPUnit_Framework_MockObject_MockObject|FarmHelper
   */
  protected $farmerHelper;
  protected function setUp()
  {
    parent::setUp();
    $this->farmerHelper = new FarmHelper();
  }
  function testFarmLifeCycle(){
    $farmId = $this->farmerHelper->CreateFarm("TitSFarm", "Tit Street",
      "www.oneoclocktit.com/fr", '+33 (0) 1 33 28 26 76', 'tiberiusferreir@gmail.com');
    $this->farmerHelper->UpdateFarm($farmId, "New Name", '', '', '', '');
    $this->farmerHelper->DeleteFarm($farmId);
  }



}
