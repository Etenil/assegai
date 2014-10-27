<?php

namespace assegai\eventsystem\events;

class Event implements IEvent
{
    protected $sender;
    protected $type = 'event';
    protected $exception;
    
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
    
    function setException(\Exception $e)
    {
        $this->exception = $e;
        return $this;
    }
    
    function getException()
    {
        return $this->exception;
    }
}
