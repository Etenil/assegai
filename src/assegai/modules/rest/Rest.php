<?php

namespace assegai\modules\rest;

use \assegai\modules;

/**
 * @package assegai.modules.rest
 *
 * This module facilitates programming REST services by simply
 * returning php arrays from functions.
 *
 * The controller's hooks are exploited by this module in order to
 * dynamically generate a JSON or XML output from an array returned
 * from the route handler.
 *
 * The class Rest_Controller is provided by this class and
 * your controllers should extend this instead of the usual
 * assegai\Controller.
 */
class Rest extends modules\Module
{
    public static function instanciate()
    {
        return false;
    }
}
