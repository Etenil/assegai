<?php

namespace assegai\modules\forms;

use \assegai\modules;
use \assegai\exceptions;

class Forms extends modules\Module
{
    protected $_renderer;

    public static function instanciate()
    {
        return true;
    }

    public function __construct()
    {
        $this->_renderer = new renderers\Paragraph();
    }

    public function preRequest(\assegai\Controller $controller, \assegai\Request $request)
    {
        $controller->register('form', array($this, 'loadForm'));

        spl_autoload_register();
    }

    public function loadForm($form_name) {
        if(stripos($form_name, 'false') === false) {
            $form_name = sprintf('%s\forms\%s', $this->server->getAppName(), $form_name);
        }

        if(!class_exists($form_name)) {
            throw new exceptions\HttpInternalServerError("Class $form_name not found");
        }
        
        return new $form_name($this->modules);
    }
    
    public function render(Form $form)
    {
        return $form->render($this->_renderer);
    }
}

