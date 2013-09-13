<?php

namespace assegai;

// Core.
Injector::register('Core', function($prefix = '') {
        $server = Injector::give('Server', $_SERVER, $prefix);
        $request = Injector::give('Request', $_GET, $_POST);
        $mc = Injector::give('ModuleContainer', $server);
        return new Core($prefix, $server, $request, $mc);
    });

// Server
Injector::register('Server', function(array $data, $prefix = '') {
        return new Server($data, $prefix);
    });

// Request
Injector::register('Request', function(array $get, array $post, array $session = null, array $cookies = null) {
        return new Request($get, $post, $session ?: array(), $cookies ?: array());
    });

// ModuleContainer
Injector::register('ModuleContainer', function(Server $server) {
        return new ModuleContainer($server);
    });

// Response
Injector::register('Response',
    function($body = '', $status_code = 200,
        $content_type = 'text/html; charset=UTF-8') {
        return new Response($body, $status_code, $content_type);
    });

// Security
Injector::register('Security', function() {
        return new Security();
    });
