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

function coreload($classname) {
    $matches = array();
    if(preg_match('/^\\\\?assegai\\\\(.+)$/', $classname, $matches)) {
        $classname = $matches[1];
        $dirpath = dirname(__FILE__);
        $potential_file = $dirpath . '/' . strtolower($classname) . '.php';
        if(file_exists($potential_file)) {
            require($potential_file);
        }
    }
}

spl_autoload_register('\assegai\coreload');

// We must force-load the exceptions for the moment.
require(dirname(__FILE__) . '/exceptions.php');

