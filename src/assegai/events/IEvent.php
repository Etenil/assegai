<?php

namespace assegai\events;

interface IEvent
{
    function getType();
    function getSender();
}
