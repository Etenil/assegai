<?php

class AssegaiRedBeanFormatter implements RedBean_IModelFormatter
{
    public function formatModel($model)
    {
        return 'RBModel_' . $model;
    }
}

class Module_Redbean extends \assegai\Module
{
    public static function instanciate()
    {
        return true;
    }

    function _init($options)
    {
        // Setting up the formatter to avoid conflict with Model_.
        $formatter = new AssegaiRedBeanFormatter();
        RedBean_ModelHelper::setModelFormatter($formatter);

        // Opening connection.
        RedBean_Facade::setup($options['dsn'], $options['user'], $options['pass']);
        RedBean_Facade::dontUseDefaultModelPrefix();
    }

    function __destruct()
    {
        RedBean_Facade::close();
    }

    /**
     * Maps calls to static redbean in a 1:1 fashion
     * (except _init and _destruct)
     */
    public function __call($method, $args)
    {
        // Exceptions
        if($method[0] == '_') {
            return call_user_func_array(array($this, $method), $args);
        } else {
            if(method_exists('RedBean_Facade', $method)) {
                return call_user_func_array(
                    array('RedBean_Facade', $method),
                    $args);
            }
        }
    }
}
