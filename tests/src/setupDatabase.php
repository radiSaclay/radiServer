<?php

function rmdir_recursive ($dir) {
  foreach(scandir($dir) as $file) {
    if ('.' === $file || '..' === $file) continue;
    if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
    else unlink("$dir/$file");
  }
  rmdir($dir);
}

function setupDir () {
  mkdir('temp');
  mkdir('temp/migration');
  return [
    'propel' => realpath(getcwd().'/propel'),
    'temp' => realpath(getcwd().'/temp'),
    'migration' => realpath(getcwd().'/temp/migration'),
  ];
}

function setupDb ($dirs) {
  $db = new sqlite3('temp/testdb.sq3');
  $path = realpath(getcwd().'/temp/testdb.sq3');
  return [
    'db' => $db,
    'path' => $path,
    'dns' => 'sqlite:'.$path,
  ];
}

function propelExec ($command, $output, $dns) {
  $cmd = './vendor/bin/propel '.$command;
  $cmd .= ' --output-dir="'.$output.'"';
  $cmd .= ' --connection="default='.$dns.'"';
  // echo $cmd . "\n\n";
  return exec($cmd);
}

function setupAll () {
  $dirs = setupDir();
  $db = setupDb($dirs);
  propelExec('migration:diff --schema-dir="'.$dirs['propel'].'"', $dirs['migration'], $db['dns']);
  propelExec('migration:migrate --config-dir="'.$dirs['propel'].'"', $dirs['migration'], $db['dns']);
  return [
    'db' => $db,
    'dirs' => $dirs,
  ];
}

function clearAll ($settings) {
  $settings['db']['db']->close();
  rmdir_recursive('temp');
}
