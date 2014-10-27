<?php

namespace assegai\eventsystem\events;

interface IEvent
{
    function getType();
    function getSender();
}
