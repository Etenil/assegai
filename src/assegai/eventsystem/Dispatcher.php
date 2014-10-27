<?php

namespace assegai\eventsystem;

class Dispatcher
{
    protected static $instance;
    protected $handlers;
    
    /**
     * This is a singleton class.
     */
    function getInstance()
    {
        if(!self::$instance || !is_object(self::$instance)) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    protected function __construct()
    {
        $this->handlers = array();
    }
    
    function register($event_type, callable $handler)
    {
        $this->handlers[$event_type][] = $handler;
        return $this;
    }
    
    function trigger(events\IEvent $event)
    {
        if(array_key_exists($event->getType(), $this->handlers)) {
            foreach($this->handlers[$event->getType()] as $handler) {
                $result = $handler($event);
                if($result) { // Returning true stops the event propagation.
                    break;
                }
            }
        }
    }
}
