<?php

define('BASE_DIR', dirname(__DIR__));

if(!file_exists(BASE_DIR . '/vendor/autoload.php')) {
    require_once(BASE_DIR . '/src/assegai/loader.php');
} else {
    require_once(BASE_DIR . '/vendor/autoload.php');
}

