<?php

namespace etenil\modules\ValidatorModule;

use \etenil\assegai\modules\Module;
use \etenil\assegai\Controller;
use \etenil\assegai\Request;

use \etenil\modules\ValidatorModule\controllers\Validator;

/**
 * Form validation library.
 *
 * @author Tasos Bekos <tbekos@gmail.com>
 * @author Chris Gutierrez <cdotgutierrez@gmail.com>
 * @author Corey Ballou <ballouc@gmail.com>
 * @see https://github.com/blackbelt/php-validation
 * @see Based on idea: http://brettic.us/2010/06/18/form-validation-class-using-php-5-3/
 */
class ValidatorModule extends Module
{
    /**
     * The validator module is a factory; you shouldn't instanciate it.
     */
    function getValidator(array $data = null)
    {
        return new Validator($data);
    }
    
    function preRequest(Controller $controller, Request $request)
    {
        $controller->register('validator', array($this, 'getValidator'));
    }
    
    static function dependencies()
    {
        $dependencies = array();
        require __DIR__ . '/dependencies.conf';
        
        return $dependencies;
    }
}
