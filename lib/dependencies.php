<?php

namespace assegai;

// Core.
$injector->register('Core', array(
    'Server', 'Request', 'ModuleContainer'
));
$injector->register('Server', array());
// Request
$injector->register('Request', array());
$injector->register('ModuleContainer', array('Server'));
$injector->register('Response');
$injector->register('Security');
