<?php

namespace assegai;

class ErrorController extends Controller
{
    /**
     * A very light view, just a glimpse of a template...
     */
    function glimpse($template, array $vars = array())
    {
        $vars = (object)$vars;
        ob_start();
        include($template);
        return ob_get_clean();
    }

    function notFoundHandler()
    {
        $e = $this->request->getException();

        if(isset($_SERVER['APPLICATION_ENV'])
        && $_SERVER['APPLICATION_ENV'] == 'development') {
            $server = $this->server;
            return $this->glimpse('templates/notfoundview.phtml', array(
                'exception' => $e,
            ));
        } else {
            return new Response('Not found!', 404);
        }
    }

    protected function printTrace(\Exception $error)
    {
        $trace = $error->getTrace();
        $formatted_trace = array();
        for($i = 0; $i < count($trace); $i++) {
            $line = '';
            if(true || strpos($trace[$i]['class'], 'assegai\\') === false) {
                $line = "$i - ";
                if($trace[$i]['class']) {
                    $line.= "at " . $trace[$i]['class'] . "::";
                }
                if($trace[$i]['function']) {
                    $line.= $trace[$i]['function'] . "() ";
                }
                $line.= sprintf("in %s on line %s",
                $trace[$i]['file'],
                $trace[$i]['line']);
            }
            $formatted_trace[] = $line;
        }

        return implode(PHP_EOL, $formatted_trace);
    }

    function errorHandler()
    {
        $e = $this->request->getException();

        if(isset($_SERVER['APPLICATION_ENV'])
            && $_SERVER['APPLICATION_ENV'] == 'development') {
            return $this->glimpse('templates/errorview.phtml', array(
                'exception' => $e,
            ));
        } else {
            return new Response(sprintf("Server error %d<br>", $e->getCode()), 500);
        }
    }
}
