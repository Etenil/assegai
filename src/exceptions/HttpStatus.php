<?php

/**
 * Multiple exceptions for assegai.
 *
 * @package exceptions
 *
 * @copyright
 * This file is part of assegai.
 *
 * assegai is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * assegai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with assegai.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace etenil\assegai\exceptions
{
    class HttpStatus extends \Exception
    {
        // Error code becomes mandatory and let's reorder them.
        function __construct($status_code, $description)
        {
            parent::__construct($description, $status_code);
        }
    }
}




















