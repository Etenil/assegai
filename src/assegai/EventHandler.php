<?php

namespace assegai;

class EventHandler
{
    protected $handlers;
    
    function register($event_type, callable $handler)
    {
        $this->handlers[$event_type][] = $handler;
    }
    
    function send(IEvent $event)
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
