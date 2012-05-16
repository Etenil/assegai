<?php

namespace assegai;

/**
 * Default controller for Assegai.
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
class Controller extends \atlatl\Controller
{
    protected $app_path;

    public function __construct(\atlatl\Server $server, \atlatl\Request $request)
    {
        parent::__construct($server, $request);
        $this->app_path = dirname(__DIR__);
    }

    protected function view($view_name, array $var_list = NULL)
    {
        if($var_list === NULL) {
            $var_list = array(); // Avoids notices.
        }

        // Traditional PHP template.
        $vars = (object)$var_list;
        ob_start();
        require($this->appPath('views/' . $view_name . '.phtml'));
        return ob_get_clean();
    }

    /**
     * Gets full path to app elements.
     * @param path is the relative path to resolve.
     * @return the corresponding absolute path.
     */
    protected function appPath($path)
    {
        return $this->app_path . '/' . $path;
    }
}

?>
