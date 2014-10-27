<?php

namespace assegai\eventsystem\events;

class Event implements IEvent
{
    protected $sender;
    protected $type = 'event';
    
    function __construct($sender = null)
    {
        $this->setSender($sender);
    }
    
    function getSender()
    {
        return $this->sender;
    }
    function setSender($sender)
    {
        $this->sender = $sender;
        return $this;
    }
    
    function getType()
    {
        return $this->type;
    }
}
