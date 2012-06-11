<?php

namespace assegai;

/**
 * Server configuration.
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
class Server extends \atlatl\Server
{
    protected $apps_path;

    public function setAppPath($val)
    {
        $this->apps_path = rtrim($val, '/');
        return $this;
    }

    public function getAppPath()
    {
        return $this->apps_path;
    }

    public function getRelAppPath($file)
    {
        return $this->apps_path . '/' . $file;
    }
}

?>
