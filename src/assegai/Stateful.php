<?php

/**
 * Response object for Assegai.
 *
 * This file is part of Assegai
 *
 * Copyright (c) 2013 Guillaume Pasquet
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace assegai;

/**
 * An object that saves cookie and sessions.
 */
class Stateful
{
    protected $sessionvars;
    protected $cookievars;
    protected $alteredsession = false;
    protected $alteredcookies = false;
    
    protected $cookies_max_age = 31557600; // One year.

    /**
     * Backwards-compatible constructor, for people who have not
     * migrated to the DI yet.
     */
    function __construct(array $cookies = null, array $session = null)
    {
        if(!$session && $this->sessionEnabled()) {
            $this->sessionvars = $_SESSION;
        } else {
            $this->sessionvars = $session ?: array();
        }

        $this->setAllCookies($cookies ?: $_COOKIE);
    }
    
    function sessionEnabled()
    {
        // PHP 5.4+ first.
        if((function_exists('session_status')
                && session_status() == PHP_SESSION_ACTIVE)
            || isset($_SESSION)) {
            return true;
        }
        else if(session_id() && isset($_SESSION)) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Whether the session has been altered.
     */
    public function alteredSession() {
        return $this->alteredsession;
    }

    /**
     * Whether the cookies have been altered.
     */
    public function alteredCookies() {
        return $this->alteredcookies;
    }

    /**
     * Sets a SESSION variable.
     * @param varname is the variable's name.
     * @param varval is the value to assign to the variable.
     * @return FALSE if session isn't started.
     */
    public function setSession($varname, $varval)
    {
        $this->alteredsession = true;
        $this->sessionvars[$varname] = $varval;
        return $this;
    }

    /**
     * Retrieves the value of a session variable.
     * @param $varname is the variable's name
     * @param $default is the default value to be returned.
     * @return the session variable or FALSE if it can't be retrieved.
     */
    public function getSession($varname, $default = false)
    {
        if(isset($this->sessionvars[$varname])) {
            return $this->sessionvars[$varname];
        } else {
            return $default;
        }
    }

    /**
     * Clears a session variable.
     * @param $varname is the session variable's name.
     */
    public function killSession($varname)
    {
        $this->alteredsession = true;
        $this->sessionvars[$varname] = null;
        return $this;
    }

    /**
     * Clears a cookie variable.
     * @param $varname is the cookie variable's name.
     */
    public function killCookie($varname)
    {
        $this->alteredcookies = true;
        $this->cookievars[$varname] = null;
        return $this;
    }
    
    /**
     * Sets the cookies max age (default is 1 year).
     * @param $val integer the number of seconds of the cookie's lifetime.
     */
    public function setCookiesMaxAge($val)
    {
        $this->cookies_max_age = $val;
        return $this;
    }
    
    /**
     * What is the cookies max age?
     * @return integer the cookies max age as a number of seconds.
     */
    public function getCookiesMaxAge()
    {
        return $this->cookies_max_age;
    }

    /**
     * Sets a COOKIE variable.
     * @param varname is the variable's name.
     * @param varval is the value to assign to the variable.
     * @param int $max_age the number of seconds before the cookie expires, defaults to $cookies_max_age.
     */
    public function setCookie($varname, $varval, $max_age = false)
    {
        $this->alteredcookies = true;
        $this->cookievars[$varname] = array(
            'value' => $varval,
            'max_age' => ($max_age !== false ? $max_age : $this->cookies_max_age),
        );
    }

    /**
     * Retrieves the value of a cookie variable.
     * @param $varname is the variable's name
     * @param $default is the default value to be returned.
     */
    public function getCookie($varname, $default = false)
    {
        if(isset($this->cookievars[$varname])) {
            return $this->cookievars[$varname]['value'];
        } else {
            return $default;
        }
    }
    
    /**
     * Gets all cookies.
     */
    function getAllCookies() {
        return array_map(
            function($cookiedef) { return $cookiedef['value']; },
            $this->cookievars
        );
    }
    
    /**
     * Sets all cookies.
     * @param array $cookies is the list of cookies as key, value pairs
     */
    function setAllCookies(array $cookies)
    {
        // Must do this for PHP 5.3 compat.
        $cookies_max_age = $this->cookies_max_age;
        $this->cookievars = array_map(
            function($cookieval) use ($cookies_max_age) {
                return array(
                    'value' => $cookieval,
                    'max_age' => $cookies_max_age,
                );
            },
            $cookies
        );
        return $this;
    }

    /**
     * Gets all session.
     */
    function getAllSession() {
        return $this->sessionvars;
    }
    
    /**
     * Sets all session variables.
     */
    public function setAllSession(array $session) {
        $this->sessionvars = $session;
    }
    
	/**
	 * Generates the page.
	 */
	public function saveState()
	{
        if($this->sessionEnabled()) {
            // Session handling.
            if(!is_array($this->sessionvars)) {
                $this->sessionvars = array();
            }
            foreach($this->sessionvars as $varname => $val) {
                if($val === null) {
                    unset($_SESSION[$varname]);
                }
                else {
                    $_SESSION[$varname] = $val;
                }
            }
        }

        if(!headers_sent()) {
            foreach($this->cookievars as $cookiename => $cookiedef) {
                if($cookiedef['value'] === null) {
                    setcookie($cookiename, null, time() - 3600, '/'); // Expiring the cookie
                }
                else {
                    setcookie(
                        $cookiename,
                        $cookiedef['value'],
                        time() + $cookiedef['max_age'],
                        '/'
                    );
                }
            }
        }
	}
}
