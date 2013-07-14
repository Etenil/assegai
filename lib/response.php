<?php

namespace assegai;

/**
 * Response object for Assegai.
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

class Response extends \atlatl\Response
{
    /**
     * Backwards-compatible constructor, for people who have not
     * migrated to the DI yet.
     */
    function __construct($body = '', $status_code = 200,
        $content_type = 'text/html; charset=UTF-8',
        array $cookies = null, array $session = null)
    {
        if(!$session) {
            // PHP 5.4+ first.
            if((function_exists('session_status')
                    && session_status() == PHP_SESSION_ACTIVE)
                || isset($_SESSION)) {
                $session = $_SESSION;
            }
            else if(session_id() && isset($_SESSION)) {
                $session = $_SESSION;
            }
            else {
                $session = array();
            }
        }

        if(!$cookies) {
            $cookies = $_COOKIE;
        }

        parent::__construct($body, $status_code, $content_type, $cookies, $session);
    }
    
    /**
     * Appends some data to the body.
     *
     * This function accepts placeholders in the form {1}, {2},
     * ... and matches the rest of parameters to those placeholders,
     * thus providing a convenient alternative to sprintf.
     *
     * @param $string is a formatted string to be added to the response.
     * @param ... matching placeholder fillers.
     */
    function append($string)
    {
        $args = func_get_args();
        if(count($args) > 1) {
            array_shift($args);
            $string = preg_replace_callback('/\\{(0|[1-9]\\d*)\\}/', function($match) use($args) {
                    return isset($args[$match[1]]) ? $args[$match[1]] : $match[0];
                },
                $string);
        }

        parent::append($string);
    }
}

?>
