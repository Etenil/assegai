<?php

namespace assegai;

/**
 * Request dispatcher.
 *
 * This is the main class of Assegai and a routing wrapper around
 * Atlatl. The principle is simple; the request is routed to the
 * correct Atlatl appliction, then the output is processed by the
 * global settings.
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
class Dispatcher
{
    protected $root_path;

    function __construct($root)
    {
        $this->root_path = $root;
        $this->parseconf();
    }

    /**
     * Parses the global configuration file and that of each
     * application.
     */
    protected function parseconf()
    {
        $apps = opendir($this->root_path . 'apps');
    }
}

?>