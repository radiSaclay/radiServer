<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\RequestBody;

function propelExec ($command) {
  $dns = 'sqlite:' . realpath(__DIR__ . '/../temp/testdb.sq3');
  $cmd = './vendor/bin/propel ' . $command;
  $cmd .= ' --output-dir="temp/migration"';
  $cmd .= ' --connection="default='.$dns.'"';
  // echo $cmd . "\n\n";
  return exec($cmd);
}

function clearDatabase () {
  EventProductQuery::create()->deleteAll();
  FarmProductQuery::create()->deleteAll();
  SubscriptionQuery::create()->deleteAll();
  PinQuery::create()->deleteAll();
  ProductQuery::create()->deleteAll();
  EventQuery::create()->deleteAll();
  FarmQuery::create()->deleteAll();
  UserQuery::create()->deleteAll();
}

$_SERVER["SERVER_ADDR"] = 'radiserver.dev';
function makeRequest ($method, $path, $body = null, $options = []) {
  // Capture STDOUT
  ob_start();
  // Prepare a mock environment
  $env = Environment::mock(array_merge([
    'REQUEST_METHOD' => $method,
    'REQUEST_URI' => $path,
    'CONTENT_TYPE' => 'application/json;charset=utf8',
  ], $options));
  // Load app
  require __DIR__ . '/../public/index.php';
  require __DIR__ . '/propel.config.php';
  // clean STDOUT
  ob_get_clean();

  $request = Request::createFromEnvironment($env);
  if ($body !== null) {
    $reqbody = new RequestBody();
    $reqbody->write(json_encode($body));
    $request = $request->withBody($reqbody);
  }
  return $app($request, new Response());
}

class ServerTestCase extends TestCase {

  public static function setUpBeforeClass () {
    // Setup dirs
    if (!is_dir('temp')) {
      mkdir('temp');
      mkdir('temp/migration');
    }
    // Setup database
    new sqlite3('temp/testdb.sq3');
    propelExec('migration:diff --schema-dir="propel"');
    propelExec('migration:migrate --config-dir="propel"');
    require __DIR__ . '/propel.config.php';
  }

  public function tearDown () {
    require __DIR__ . '/propel.config.php';
    clearDatabase();
  }

}
