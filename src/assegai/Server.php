<?php

/**
 * Server configuration.
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

class Server 
{
    /** HTTP Method. */
	protected $method;
    /** Server's hostname. */
	protected $host;
    /** User agent. */
	protected $user_agent;
    /** What the client accepts. */
	protected $accept;
    /** Languages requested by the client. */
	protected $languages;
    /** Encodings supported by client. */
	protected $encodings;
    /** Type of HTTP connection. */
	protected $connection;
    /** Referring URL. */
	protected $referer;
    /** Requested path. */
	protected $path;
    /** Operating system PATH variable. */
	protected $software;
    /** Server name. */
	protected $name;
    /** Server's address. */
	protected $address;
    /** Server's port. */
	protected $port;
    /** Client address. */
	protected $remote_addr;
    /** Client port. */
	protected $remote_port;
    /** Admin's email address. */
	protected $admin;
    /** Requested filename. */
	protected $filename;
    /** Current script's name. */
	protected $scriptname;
    /** Request protocol. */
	protected $protocol;
    /** Requested URI. */
	protected $uri;
    /** Request time (Unix epoch). */
	protected $time;
    /** Requested route. */
	protected $route;
    /** Server prefix. */
    protected $prefix;

    /** Path to the applications */
    protected $apps_path;

    /** The app that is currently run */
    protected $app_name;

    /** Main configuration */
    public $main;
    /** App configuration */
    public $app;

    protected static $instance;


    /**
     * Constructs the server object.
     * @param array $server is a server state array; typically $_SERVER.
     */
	protected function __construct()
	{
		$this->parsevars($_SERVER);
        $this->prefix = '';
	}

    /**
     * Yes, this is a singleton...
     */
    public static function getInstance()
    {
        if(!is_object(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /** Gets the current route without prefix. */
	public function getRoute()
    {
        $route = substr($this->route, strlen($this->prefix));
        // Stripping final '/'.
        if(strlen($route) > 1
           && $route[strlen($route) - 1] == '/') {
            $route = substr($route, 0, strlen($route) - 1);
        }

        return $route;
    }

	/**
	 * Parses server variables into this class.
     * @param array $s is a server state array, like $_SERVER.
	 */
	protected function parsevars(array $s)
	{
		$this->method      = strtoupper($this->arrayGet('REQUEST_METHOD', $s));
		$this->host        = $this->arrayGet('HTTP_HOST', $s);
		$this->user_agent  = $this->arrayGet('HTTP_USER_AGENT', $s, false, false);
		$this->accept      = $this->arrayGet('HTTP_ACCEPT', $s, true);
		$this->languages   = $this->arrayGet('HTTP_ACCEPT_LANGUAGE', $s, true);
		$this->encodings   = $this->arrayGet('HTTP_ACCEPT_ENCODING', $s, true);
		$this->connection  = $this->arrayGet('HTTP_CONNECTION', $s);
		$this->referer     = $this->arrayGet('HTTP_REFERER', $s);
		$this->path        = $this->arrayGet('PATH', $s);
		$this->software    = $this->arrayGet('SERVER_SOFTWARE', $s);
		$this->name        = $this->arrayGet('SERVER_NAME', $s);
		$this->address     = $this->arrayGet('SERVER_ADDR', $s);
		$this->port        = $this->arrayGet('SERVER_PORT', $s);
		$this->remote_addr = $this->arrayGet('REMOTE_ADDR', $s);
		$this->remote_port = $this->arrayGet('REMOTE_PORT', $s);
		$this->admin       = $this->arrayGet('SERVER_ADMIN', $s);
		$this->filename    = $this->arrayGet('SCRIPT_FILENAME', $s);
		$this->scriptname  = $this->arrayGet('SCRIPT_NAME', $s);
		$this->uri         = $this->arrayGet('REQUEST_URI', $s);
		$this->time        = $this->arrayGet('REQUEST_TIME', $s);

        if($this->arrayGet('HTTPS', $s) || strtolower($this->arrayGet('HTTP_X_FORWARDED_PROTO', $s)) == 'https') {
            $this->protocol = 'https';
        } else {
            $this->protocol = 'http';
        }

		// Stripping GET and anchor from the URI.
		$this->route = $this->uri;
		$get_id = strpos($this->route, '?');
		if($get_id !== false) {
			$this->route = substr($this->route, 0, $get_id);
		}
		$anchor_id = strpos($this->route, '#');
		if($anchor_id !== false) {
			$this->route = substr($this->route, 0, $anchor_id);
		}
	}

    /**
     * Gets, checks and cleans an array entry. Avoids warnings and
     * simplifies use.
     * @param string $key is the key to fetch from the array.
     * @param array $array is the array to get the element from.
     * @param boolean $split indicates whether the value is
     * comma-separated and should be split into an array. The default
     * is FALSE.
     * @return the cleaned up or broken up value corresponding to the
     * requested $key. If $key doesn't exist in $array, then FALSE is
     * returned.
     */
    protected function arrayGet($key, array $array, $split = false, $clean = true)
    {
        if(isset($array[$key])) {
			if($split) {
                if($clean) {
                    $data = explode(',', $this->clean($array[$key]));
                } else {
                    $data = explode(',', $array[$key]);
                }
				$data = array_map(function($item) { return trim($item); }, $data);
				return $data;
			} else {
                if($clean) {
                    return $this->clean($array[$key]);
                } else {
                    return $array[$key];
                }
			}
        } else {
            return false;
        }
    }

	/**
	 * Cleans up server entries.
     * @param string $serverstring is a server's property value.
     * @return the cleaned up $serverstring.
	 */
	protected function clean($serverstring)
	{
		// Cleaning the `...;q=x.x' and in general anything after ';'.
		$scol_id = strpos($serverstring, ';');
		if($scol_id !== false) {
			return substr($serverstring, 0, $scol_id);
		} else {
			return $serverstring;
		}
	}

    /**
     * Gets the whole of POST data from the request as a string.
     * @return string the POST data.
     */
    public function getWholePost()
    {
        return file_get_contents('php://input');
    }

    public function siteUrl($url)
    {
        $siteurl = $this->getProtocol() . '://' . $this->getHost();
        //$siteurl = 'http://' . $this->getHost();

        if($this->getPort() != 80 && $this->getPort() != 443 && !strpos($this->getHost(), ':')) {
            $siteurl.= ':' . $this->getPort();
        }

        $siteurl = Utils::joinPaths($siteurl, $this->getPrefix(), $url);

        return $siteurl;
    }

// Accessors
    /** Accessor for $method. */
	public function getMethod()
		{ return $this->method; }
    /** Accessor for $host. */
	public function getHost()
		{ return $this->host; }
    /** Accessor for $user_agent. */
	public function getUserAgent()
		{ return $this->user_agent; }
    /** Accessor for $accept. */
	public function getAccept()
		{ return $this->accept; }
    /** Accessor for $languages. */
	public function getLanguages()
		{ return $this->languages; }
    /** Accessor for $encodings. */
	public function getEncodings()
		{ return $this->encodings; }
    /** Accessor for $connection. */
	public function getConnection()
		{ return $this->connection; }
    /** Accessor for $referer. */
	public function getReferer()
		{ return $this->referer; }
    /** Accessor for $path. */
	public function getPath()
		{ return $this->path; }
    /** Accessor for $software. */
	public function getSoftware()
		{ return $this->software; }
    /** Accessor for $name. */
	public function getName()
		{ return $this->name; }
    /** Accessor for $address. */
	public function getAddress()
		{ return $this->address; }
    /** Accessor for $port. */
	public function getPort()
		{ return $this->port; }
    /** Accessor for $remote_addr. */
	public function getRemoteAddr()
		{ return $this->remote_addr; }
    /** Accessor for $remote_port. */
	public function getRemotePort()
		{ return $this->remote_port; }
    /** Accessor for $admin. */
	public function getAdmin()
		{ return $this->admin; }
    /** Accessor for $filename. */
	public function getFilename()
		{ return $this->filename; }
    /** Accessor for $scriptname. */
	public function getScriptname()
		{ return $this->scriptname; }
    /** Accessor for $protocol. */
	public function getProtocol()
		{ return $this->protocol; }
    /** Accessor for $uri. */
	public function getUri()
		{ return $this->uri; }
    /** Accessor for $time. */
	public function getTime()
		{ return $this->time; }
    /** Gets the whole current route. */
    public function getWholeRoute()
        { return $this->route; }
    /** Accessor for $prefix. */
    public function getPrefix()
        { return $this->prefix; }
    public function setPrefix($val)
        { $this->prefix = $val; return $this; }

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
        return Utils::joinPaths($this->apps_path, $file);
    }

    public function setMainConf(Config $main_conf)
    {
        $this->main = $main_conf;
    }

    public function setAppConf(Config $app_conf)
    {
        $this->app = $app_conf;
    }

    public function setAppName($app)
    {
        $this->app_name = $app;
        return $this;
    }

    public function getAppName()
    {
        return $this->app_name;
    }
}

?>
