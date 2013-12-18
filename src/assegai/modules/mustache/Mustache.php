<?php

namespace assegai\modules\mustache;

/**
 * @package assegai.modules.mustache
 *
 * Mustache templating engine module for PHP.
 */
class Mustache extends \assegai\Module
{
    public static function instanciate()
    {
        return true;
    }

    public function preView(assegai\Request $request, $tpl, $vars)
    {
        $tpl_path = $this->server->getRelAppPath('views/' . $tpl . '.tpl');
        if(!file_exists($tpl_path)) {
            throw new \assegai\NoViewException("View `$tpl_path' doesn't exist");
        }

        $m = new MustacheEngine($this->server);
        return $m->render(file_get_contents($tpl_path), (array)$vars);
    }
}
