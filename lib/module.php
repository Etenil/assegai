<?php

namespace assegai;

/**
 * Basic module implementation.
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
class Module extends \atlatl\Module
{
    /**
     * Whether to instanciate and attach the module upon loading.
     * @return bool true if instanciation is needed, or false.
     */
    public static function instanciate()
    {
        return false;
    }

    public function model($name) {
        return new $name($this->modules);
    }
}
