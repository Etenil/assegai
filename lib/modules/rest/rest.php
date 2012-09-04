<?php

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
class Module_Rest extends \assegai\Module
{
    public static $instanciate = false;
}

class Rest_Controller extends \assegai\Controller
{
    const REST_JSON = 1;
//    const REST_XML = 2;

    protected $rest_type = self::REST_JSON;

    public function postRequest($returned)
    {
        $response = new \assegai\Response();

        if($this->rest_type == self::REST_JSON) {
            $response->setBody(json_encode($returned));
            $response->setHeader('Content-Type', 'application/json');
        }
/*        else if($this->rest_type == REST_XML) {
            $response->setBody(
            }*/
        return $response;
    }
}
