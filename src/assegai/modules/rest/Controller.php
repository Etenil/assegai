<?php

namespace assegai\modules\rest;

class Controller extends \assegai\Controller
{
    const REST_JSON = 1;
//    const REST_XML = 2;

    protected $rest_type = self::REST_JSON;
    protected $post;

    protected function _init()
    {
        // Storing the whole POST content here.
        $this->post = file_get_contents('php://input');
    }

    public function postRequest($returned)
    {
        $response = new \assegai\Response();

        if ($this->rest_type == self::REST_JSON) {
            $response->setBody(json_encode($returned));
            $response->setHeader('Content-Type', 'application/json');
        }
/*        else if($this->rest_type == REST_XML) {
            $response->setBody(
            }*/
        return $response;
    }
}
