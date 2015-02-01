<?php

namespace etenil\modules\MustacheModule;

use \etenil\assegai\modules;
use \etenil\assegai\Request;
use \etenil\assegai\exceptions;

use \etenil\modules\MustacheModule\views\MustacheEngine;

/**
 * @package assegai.modules.mustache
 *
 * Mustache templating engine module for PHP.
 */
class MustacheModule extends modules\Module
{
    public static function instanciate()
    {
        return true;
    }

    public function preView(Request $request, $tpl, $vars)
    {
        $tpl_path = $this->server->getRelAppPath('views/' . $tpl . '.tpl');
        if(!file_exists($tpl_path)) {
            throw new exceptions\NoViewException("View `$tpl_path' doesn't exist");
        }

        $m = new MustacheEngine($this->server);
        return $m->render(file_get_contents($tpl_path), (array)$vars);
    }
}
