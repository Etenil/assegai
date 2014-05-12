<?php

define('BASE_DIR', dirname(__DIR__));

if(!file_exists(BASE_DIR . '/vendor/autoload.php')) {
    require_once(BASE_DIR . '/lib/loader.php');
} else {
    require_once(BASE_DIR . '/vendor/autoload.php');
}

