<?php

/**
 * @package assegai.modules.mail
 *
 * This module facilitates usage of MAIL services by providing a
 * unified interface to several services.
 */
class Module_Mail extends \assegai\Module
{
    protected $svc;
    
    public static function instanciate()
    {
        return true;
    }

    protected function _init($options)
    {
        $classfile = __DIR__ . "/services/builtin.php"; // Default
        $classname = "\\assegai\\module\\mail\\BuiltinService";
        
        if(isset($options['service'])) { // We use the standard email service
            if(file_exists(__DIR__."/services/".$options['service']."/".$options['service'].".php")) {
                $classfile = __DIR__."/services/".$options['service']."/".$options['service'].".php"
            }
            else if(file_exists(__DIR__."/services/".$options['service'].".php")) {
                $classfile = __DIR__."/services/".$options['service'].".php"
            }
            $classname = "\\assegai\\module\\mail\\" . ucwords($options['service']) . "Service";
        }

        require($classfile);
        $this->svc = new $classname($options['options']);
    }

    // Just a nice wrapper to get an email object.
    public function newEmail()
    {
        return new \assegai\module\mail\Email();
    }

    // Sends an email through the loaded service.
    public function send(Email $email)
    {
        return $this->svc->send($email);
    }
}
