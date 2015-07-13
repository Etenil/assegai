<?php

/*
 * Copyright (C) 2015  Guillaume Pasquet
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
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

namespace Assegai\Core;

use Assegai\Core\Exception\ConfigFileNotFoundException;

class Config {
    /** @var array the list of configuration definitions */
    protected $definitions = array();
    
    /**
     * Load a definitions array in the config.
     * @param array $definitions is the array of definitions to add to config.
     */
    public function loadArray(array $definitions) {
        $this->definitions = array_merge($this->definitions, $definitions);
    }
    
    /**
     * Loads up a list of definitions from a JSON file.
     * @param string $conf_file is the path to the JSON config file to load.
     */
    public function loadFile($conf_file) {
        if(!file_exists($conf_file)) {
            throw new 
            $this->loadArray(json_decode($conf_file));
        }
    }
    
    /**
     * Get a value from the config dictionary. It is possible to traverse the
     * dictionary through dotted notation i.e. foo => [bar => baz] can be
     * accessed as foo.bar.
     * @param string $key is the config key to fetch.
     * @return mixed the config value required, FALSE if not set.
     */
    public function get($key) {
        $traversal = explode('.', $key);
        
        $value = $this->definitions;
        foreach($traversal as $level_key) {
            if(!array_key_exists($level_key, $value)) {
                if(in_array($level_key, $value)) {
                    return true;
                }
                return false;
            } else {
                $value = $value[$level_key];
            }
        }
        
        return $value;
    }
}
