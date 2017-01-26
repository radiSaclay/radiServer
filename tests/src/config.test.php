<?php

$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$serviceContainer->checkVersion('2.0.0-dev');
$serviceContainer->setAdapterClass('default', 'sqlite');
$manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
$manager->setConfiguration([
  'dsn' => 'sqlite:'.realpath(getcwd().'/temp/testdb.sq3'),
  'user' => 'root',
  'password' => '',
  'settings' => [ 'charset' => 'utf8', 'queries' => [] ],
  'classname' => '\\Propel\\Runtime\\Connection\\ConnectionWrapper',
  'model_paths' => [ 0 => 'src', 1 => 'vendor' ],
]);
$manager->setName('default');
$serviceContainer->setConnectionManager('default', $manager);
$serviceContainer->setDefaultDatasource('default');
