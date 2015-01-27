<?php

namespace assegai\modules\mail
{
    use \assegai\modules;
    
    /**
     * @package assegai.modules.mail
     *
     * This module facilitates usage of MAIL services by providing a
     * unified interface to several services.
     */
    class Mail extends modules\Module
    {
        protected $svc;
        protected $default_sender;
        
        public static function instanciate()
        {
            return true;
        }

        function setOptions($options)
        {
            parent::setOptions($options);

            $this->default_sender = @$options['sender'];
            
            $classname = "\\assegai\\modules\\mail\\services\\Builtin";

            if(isset($options['service'])) { // We use the standard email service
                if(class_exists($options['service']) && in_array( 'assegai\modules\mail\Service', class_implements($options['service']))) {
                    $classname = $options['service'];
                }
                else {
                    $classname = "\\assegai\\modules\\mail\\services\\" . ucwords($options['service']);
                }
            }

            $this->svc = new $classname(@$options['options']);
        }

        // Just a nice wrapper to get an email object.
        public function newEmail()
        {
            $e = new Email();
            if($this->default_sender) {
                $e->setSender($this->default_sender);
            }
            
            return $e;
        }

        // Sends an email through the loaded service.
        public function send(Email $email)
        {
            return $this->svc->send($email);
        }
    }
}
