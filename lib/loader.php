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

if(file_exists(__DIR__ . '/atlatl/loader.php')) {
    require(__DIR__ . '/atlatl/loader.php');
}
elseif(file_exists(__DIR__ . '/atlatl.php')) {
    require(__DIR__ . '/atlatl.php');
}
else {
    throw new \Exception("Please run the build.sh script.");
}

require('dispatcher.php');
require('server.php');
require('response.php');
require('modulecontainer.php');
require('module.php');
require('controller.php');
require('model.php');

?>
