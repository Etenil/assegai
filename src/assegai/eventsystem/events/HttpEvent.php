<?php

namespace assegai\eventsystem\events;

class HttpEvent extends Event implements IEvent
{
    protected $type = 'http';
    
    /** Requested route. */
    protected $route;
    /** Whole route */
    protected $whole_route;
    /** Request's HTTP method (GET, POST, whatnot). */
    protected $method;
    /** Stores the GET variables. */
	protected $getvars;
    /** Stores the POST variables. */
	protected $postvars;
    
    /** Security */
    protected $sec;
    protected $server;
    
    function setServer(\assegai\Server $server)
    {
        $this->server = $server;
        return $this;
    }
    
    function setSecurity(\assegai\Security $sec)
    {
        $this->sec = $sec;
        return $this;
    }
    
    function loadGlobals()
    {
        $this->route = $this->server->getRoute();
        $this->whole_route = $this->server->getWholeRoute();
        $this->method = $this->server->getMethod();
		$this->getvars = $_GET;
		$this->postvars = $_POST;
        return $this;
    }

    public function getRoute() {
        return $this->route;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getWholeRoute() {
        return $this->whole_route;
    }

    function setRoute($val) {
        $this->route = $val;
        return $this;
    }

    function setWholeRoute($val) {
        $this->whole_route = $val;
        return $this;
    }

    function setMethod($val) {
        $this->method = $val;
        return $this;
    }

	/**
	 * Retrieves a GET variable.
	 * @param string    $varname         The variable to fetch.
	 * @param mixed     $default         Default value to return,
	 * FALSE is the default.
	 */
	public function unsafeGet($varname, $default = false)
	{
		if(isset($this->getvars[$varname])) {
			return $this->getvars[$varname];
		} else {
			return $default;
		}
	}

    /**
     * Returns a string with all GET parameters as they appear in the URL.
     */
    public function getToString()
    {
        $get = $this->allGet();
        $formatted = array();
        foreach($get as $name => $val) {
            $formatted[] = "$name=$val";
        }

        return implode('&', $formatted);
    }

	/**
	 * Retrieves a POST variable.
	 * @param string    $varname         The variable to fetch.
	 * @param mixed     $default         Default value to return,
	 * FALSE is the default.
	 */
	public function unsafePost($varname, $default = false)
	{
		if(isset($this->postvars[$varname])) {
			return $this->postvars[$varname];
		} else {
			return $default;
		}
	}

    /**
     * Retrieves all POST variables.
     */
    public function unsafeAllPost()
    {
        return $this->postvars;
    }

    /**
     * Retrieves all GET variables.
     */
    public function unsafeAllGet()
    {
        return $this->getvars;
    }

    /**
     * Returns an escaped post variable or default.
     * @param $varname is the variable's name.
     * @param $default is the default to be returned if the variable
     * doesn't exist.
     */
    function post($varname, $default = false)
    {
        return $this->sec->clean($this->unsafePost($varname, $default));
    }

    /**
     * Returns an escaped get variable or default.
     * @param $varname is the variable's name.
     * @param $default is the default to be returned if the variable
     * doesn't exist.
     */
    function get($varname, $default = false)
    {
        return $this->sec->clean($this->unsafeGet($varname, $default));
    }

    /**
     * Returns all escaped post data as an array.
     */
    function allPost()
    {
        $post = $this->unsafeAllPost();
        return array_map(array($this->sec, 'clean'), $post);
    }

    /**
     * Returns all escaped get data as an array.
     */
    function allGet()
    {
        $get = $this->unsafeAllGet();
        return array_map(array($this->sec, 'clean'), $get);
    }

    /**
     * Does the request contain files?
     */
    function hasFiles()
    {
        return (count($_FILES) > 0);
    }

    function _getAndMoveFile($file, $target, $exts = null)
    {
        $target_filename = basename($target);
        $target_dir = dirname($target);
        $target = Utils::joinPaths($target_dir, Utils::cleanFilename($target_filename));

        if($file['error'] > 0) {
            throw new FileUploadError($file['error']);
        }

        if($exts != NULL && count($exts > 0)) {
            $allowed = false;
            foreach($exts as $ext) {
                if(preg_match("#".$ext.'$#', $target)) {
                    $allowed = true;
                    break;
                }
            }

            if(!$allowed) {
                throw new \Exception("The uploaded file is not allowed.");
            }
        }

        if(move_uploaded_file($file['tmp_name'], $target)) {
            return $target;
        } else {
            return false;
        }
    }

    /**
     * Gets an uploaded file.
     */
    function getFile($slot_name, $target, $exts = NULL, $generate_names = false)
    {
        if(!$target) {
            throw new \Exception("Can't move file without destination.");
        }

        if(!array_key_exists($slot_name, $_FILES)) {
            return false;
        }

        $return = false;

        // Several files uploaded with the same name like uploads[].
        if(is_array($_FILES[$slot_name]['name'])) {
            if(!is_dir($target)) {
                throw new Exception("Target `$target' must be a directory for several files.");
            }

            $return = array();
            for($file_num = 0; $file_num < count($_FILES[$slot_name]['name']); $file_num++) {
                $file = array(
                    'name' => $_FILES[$slot_name]['name'][$file_num],
                    'type' => $_FILES[$slot_name]['type'][$file_num],
                    'tmp_name' => $_FILES[$slot_name]['tmp_name'][$file_num],
                    'error' => $_FILES[$slot_name]['error'][$file_num],
                    'size' => $_FILES[$slot_name]['size'][$file_num]
                    );

                $looptarget = '';
                if($generate_names) {
                    // Getting the ext. I don't use pathinfo() because the file may not exist...
                    $ext = substr(strrchr($file['name'], '.'), 0);
                    $looptarget = Utils::uniqueFilename($target, '', $ext);
                } else {
                    $looptarget = Utils::joinPaths($target, basename($file['name']));
                }

                try {
                    $return[] = $this->_getAndMoveFile($file, $looptarget, $exts);
                }
                catch(FileUploadError $e) {
                    $return[] = $e;
                }
            }
        } else {
            if(is_dir($target)) {
                if($generate_names) {
                    $ext = substr(strrchr($_FILES[$slot_name]['name'], '.'), 0);
                    $target = Utils::uniqueFilename($target, '', $ext);
                } else {
                    $target = Utils::joinPaths($target, basename($_FILES[$slot_name]['name']));
                }
            }

            try {
                $return = $this->_getAndMoveFile($_FILES[$slot_name], $target, $exts);
            }
            catch(FileUploadError $e)
            {
                return $e;
            }
        }

        return $return;
    }
}
