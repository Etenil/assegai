<?php

$_SERVER['APPLICATION_ENV'] = 'development';

require_once('src/assegai/loader.php');

$c = assegai\Utils::bootstrapContainer();

$framework = $c->give('framework')->setConfigPath(__DIR__ . '/conf.php');
$framework->run($c->give('httpEvent')->loadGlobals());
