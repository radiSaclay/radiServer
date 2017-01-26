<?php
require_once __DIR__ . '/src/setupDatabase.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

function makeRequest ($method, $path, $options = []) {
  // Capture STDOUT
  ob_start();
  // Prepare a mock environment
  $env = Environment::mock(array_merge([
    'REQUEST_METHOD' => $method,
    'REQUEST_URI' => $path,
  ]), $options);
  // Load app
  require __DIR__ . '/../public/index.php';
  require __DIR__ . '/src/config.test.php';
  // clean STDOUT
  ob_get_clean();

  $request = Request::createFromEnvironment($env);
  return $app($request, new Response());
}

class ServerTestCase extends TestCase {
  protected static $settings;
  protected static $app;

  public static function setUpBeforeClass () {
    self::$settings = setupAll();
  }

  public static function tearDownAfterClass () {
    clearAll(self::$settings);
    self::$settings = null;
  }

}
