<?php

namespace assegai;

/**
 * Request object for Assegai.
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

class Request extends \atlatl\Request
{
    protected $sec;

    /**
     * Initialises this request object.
     * @param $get is a GET associative array.
     * @param $post is a POST associative array.
     * @param $sec is an instance of atlatl's security class: \atlatl\Security.
     */
    function __construct(array $get, array $post, \atlatl\Security $sec, $session, $cookies)
    {
        $this->sec = $sec;

        parent::__construct($get, $post, $session, $cookies);
    }

    /**
     * Returns an escaped post variable or default.
     * @param $varname is the variable's name.
     * @param $default is the default to be returned if the variable
     * doesn't exist.
     */
    function post($varname, $default = false)
    {
        return $this->sec->clean(parent::post($varname, $default));
    }

    /**
     * Returns an escaped get variable or default.
     * @param $varname is the variable's name.
     * @param $default is the default to be returned if the variable
     * doesn't exist.
     */
    function get($varname, $default = false)
    {
        return $this->sec->clean(parent::get($varname, $default));
    }

    /**
     * Returns an unescaped post variable or default.
     * @param $varname is the variable's name.
     * @param $default is the default to be returned if the variable
     * doesn't exist.
     */
    function unsafePost($varname, $default = false)
    {
        return parent::post($varname, $default);
    }

    /**
     * Returns an unescaped get variable or default.
     * @param $varname is the variable's name.
     * @param $default is the default to be returned if the variable
     * doesn't exist.
     */
    function unsafeGet($varname, $default = false)
    {
        return parent::get($varname, $default);
    }

    /**
     * Returns all escaped post data as an array.
     */
    function allPost()
    {
        $post = parent::allPost();
        return array_map(array($this->sec, 'clean'), $post);
    }

    /**
     * Returns all escaped get data as an array.
     */
    function allGet()
    {
        $get = parent::allGet();
        return array_map(array($this->sec, 'clean'), $get);
    }

    /**
     * Returns all unescaped post data as an array.
     */
    function unsafeAllPost()
    {
        return parent::allPost();
    }

    /**
     * Returns all unescaped get data as an array.
     */
    function unsafeAllGet()
    {
        return parent::allGet();
    }
}

?>
