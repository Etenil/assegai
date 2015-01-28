<?php

namespace etenil\assegai\modules\mustache;

use \etenil\assegai\modules;

/**
 * @package assegai.modules.mustache
 *
 * Mustache templating engine module for PHP.
 */
class Mustache extends modules\Module
{
    public static function instanciate()
    {
        return true;
    }

    public function preView(etenil\assegai\Request $request, $tpl, $vars)
    {
        $tpl_path = $this->server->getRelAppPath('views/' . $tpl . '.tpl');
        if(!file_exists($tpl_path)) {
            throw new \etenil\assegai\NoViewException("View `$tpl_path' doesn't exist");
        }

        $m = new MustacheEngine($this->server);
        return $m->render(file_get_contents($tpl_path), (array)$vars);
    }
}
