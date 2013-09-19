<?php

require_once(__DIR__ . '/email.php');
require_once(__DIR__ . '/service.php');

/**
 * @package assegai.modules.mail
 *
 * This module facilitates usage of MAIL services by providing a
 * unified interface to several services.
 */
class Module_Mail extends \assegai\Module
{
    protected $svc;
    protected $default_sender;
    
    public static function instanciate()
    {
        return true;
    }

    function _init($options)
    {
        $this->default_sender = $options['sender'];
        
        $classfile = __DIR__ . "/services/builtin.php"; // Default
        $classname = "\\assegai\\module\\mail\\BuiltinService";
        
        if(isset($options['service'])) { // We use the standard email service
            if(file_exists(__DIR__."/services/".$options['service']."/".$options['service'].".php")) {
                $classfile = __DIR__."/services/".$options['service']."/".$options['service'].".php";
            }
            else if(file_exists(__DIR__."/services/".$options['service'].".php")) {
                $classfile = __DIR__."/services/".$options['service'].".php";
            }
            $classname = "\\assegai\\module\\mail\\" . ucwords($options['service']) . "Service";
        }

        require_once($classfile);
        $this->svc = new $classname($options['options']);
    }

    // Just a nice wrapper to get an email object.
    public function newEmail()
    {
        $e = new \assegai\module\mail\Email();
        if($this->default_sender) {
            $e->setSender($this->default_sender);
        }
        
        return $e;
    }

    // Sends an email through the loaded service.
    public function send(\assegai\module\mail\Email $email)
    {
        return $this->svc->send($email);
    }
}
