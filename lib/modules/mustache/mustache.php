<?php

require('MustacheEngine.php');

/**
 * Mustache templating engine module for PHP.
 */
class Module_Mustache extends \assegai\Module
{
    public function preView(\atlatl\Request $request, $tpl, $vars)
    {
        $tpl_path = $this->server->getRelAppPath('views/' . $tpl . '.tpl');
        if(!file_exists($tpl_path)) {
            throw new \atlatl\NoViewException("View `$tpl_path' doesn't exist");
        }

        $m = new MustacheEngine();
        return $m->render(file_get_contents($tpl_path), (array)$vars);
    }
}
