<?php

require('MustacheEngine.php');

/**
 * @parents assegai.modules.mustache
 *
 * Mustache templating engine module for PHP.
 */
class Module_Mustache extends \assegai\Module
{
    public static function instanciate()
    {
        return true;
    }

    public function preView(\atlatl\Request $request, $tpl, $vars)
    {
        $tpl_path = $this->server->getRelAppPath('views/' . $tpl . '.tpl');
        if(!file_exists($tpl_path)) {
            throw new \atlatl\NoViewException("View `$tpl_path' doesn't exist");
        }

        $m = new MustacheEngine($this->server);
        return $m->render(file_get_contents($tpl_path), (array)$vars);
    }
}
