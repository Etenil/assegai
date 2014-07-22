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

        if(!$cookies) {
            $this->cookievars = $_COOKIE;
        } else {
            $this->cookievars = $cookies ?: array();
        }
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
     */
    public function setCookie($varname, $varval)
    {
        $this->alteredcookies = true;
        $this->cookievars[$varname] = $varval;
    }

    /**
     * Retrieves the value of a cookie variable.
     * @param $varname is the variable's name
     * @param $default is the default value to be returned.
     */
    public function getCookie($varname, $default = false)
    {
        if(isset($this->cookievars[$varname])) {
            return $this->cookievars[$varname];
        } else {
            return $default;
        }
    }
    
    /**
     * Gets all cookies.
     */
    function getAllCookies() {
        return $this->cookievars;
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
     * Sets all cookie variables.
     */
    public function setAllCookies(array $cookies) {
        $this->cookievars = $cookies;
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

        foreach($this->cookievars as $cookiename => $cookieval) {
            if($cookieval === null) {
                setcookie($cookiename, $cookieval, time() - $this->cookies_max_age, '/');
            }
            else {
                setcookie($cookiename, $cookieval, time() + $this->cookies_max_age, '/');
            }
        }
	}
}
