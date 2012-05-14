<?php

namespace assegai;

/**
 * This is a class that parses and represents an RC file.
 *
 * It will create a cached pre-parsed version of the RC file within
 * the temporary files directory. This will be compared to the md5 of
 * the RC file and loaded if no changes were made.
 *
 * That can potentially create a lot of cache files, but then you're
 * not supposed to change an RC file that often...
 *
 * The RC file syntax is very simple. It only supports string and
 * array types like so:
 *
 * def1 = "string"
 * def2 = 'string'
 * def3 = (string1 string2)
 *
 * Please note that arrays cannot be nested and that definitions are
 * case insensitive.
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
class RCFile
{
    protected $defs; // Contains definitions.

    public function __construct($file_path) {
        $this->parse($file_path);
    }

    /**
     * Does the actual parsing.
     */
    protected function parse($file_path)
    {
        if(!file_exists($file_path) || is_dir($file_path)) {
            throw new RCException("No such RC file: `$file_path'");
        }

        $file = file($file_path);
        if($file === false) {
            throw new RCException("Couldn't open RC file: `$file_path'");
        }

        // Let's md5 it for now.
        $cache_file = sys_get_temp_dir() . '/' . md5_file($file_path);

        // Do we have a cache?
        if(file_exists($cache_file)) {
            $this->defs = unserialize(file_get_contents($cache_file));
        } else {
            // Parsing for real.
            $lines = file($file_path);
            foreach($lines as $line) {
                $line = trim(preg_replace('%#.+$%', '', $line));
                if('' == $line) { // Empty line or comments
                    continue;
                }
                // This is a definition.
                $matches = array();
                preg_match_all('#([a-zA-Z]\w*)\s*=\s*([\'"(])(.+)([\'")])#', $line, $matches);
                if($matches[2][0] == '(') { // Array
                    $this->defs[strtolower($matches[1][0])] = explode(" ", $matches[3][0]);
                } else { // value
                    $this->defs[strtolower($matches[1][0])] = $matches[3][0];
                }
            }

            // Caching.
            file_put_contents($cache_file, serialize($this->defs));
        }
    }

    /**
     * Returns the settings for a definition.
     * @param def_name is the definition's name.
     * @return the value that matches the definition or FALSE.
     */
    public function get($def_name)
    {
        if(isset($this->defs[$def_name])) {
            return $this->defs[$def_name];
        } else {
            return false;
        }
    }

    /**
     * Returns the value of a definition as boolean. If the definition
     * is YES, returns TRUE, otherwise FALSE. If there is no
     * definition, it returns NULL (which can be assimilated to NULL
     * or considered separately with ===).
     *
     * @param def_name the definition name.
     * @return True or False.
     */
    public function getBool($def_name)
    {
        $val = $this->get($def_name);
        if(!$val) {
            return NULL;
        }
        else if(strtolower($val) == "yes") {
            return true;
        }
        else {
            return false;
        }
    }
}

?>