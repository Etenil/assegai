<?php

namespace assegai;

/**
 * Loads the necessary files for Assegai to work.
 *
 * This file is part of Assegai
 *
 * Assegai is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * Assegai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Assegai.  If not, see <http://www.gnu.org/licenses/>.
 */

// Attempting to load the composer-loaded stuff from vendor/etenil/atlatl/loader.php
if(!class_exists("\\atlatl\\Utils")) {
    // Right, Maybe that's a stand-alone install and we need to load composer ourselves.
    if(file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
        require(dirname(__DIR__) . '/vendor/autoload.php');
    }
    // OK, maybe it's within the current folder like in old times...
    else if(file_exists(__DIR__ . '/atlatl/loader.php')) {
        require(__DIR__ . '/atlatl/loader.php');
    }
    // Finally it might be all packaged into a single file in lib.
    elseif(file_exists(__DIR__ . '/atlatl.php')) {
        require(__DIR__ . '/atlatl.php');
    }
    else {
        throw new \Exception("Please install the atlatl dependency.");
    }
}

require('config.php');
require('utils.php');
require('dispatcher.php');
require('server.php');
require('request.php');
require('response.php');
require('modulecontainer.php');
require('module.php');
require('icontroller.php');
require('controller.php');
require('model.php');

?>
