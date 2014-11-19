<?php

/**
 * This file is part of Assegai
 *
 * Copyright (c) 2013 Guillaume Pasquet
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE Warray_slice($matches, 1)ARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace assegai\routing;

use \assegai\eventsystem\events;

class DefaultRouter implements IRouter
{
    protected $routes;

    public function __construct()
    {
        $this->routes = array();
    }

    /**
     * Sets the routes for this router.
     */
    function setRoutes($app, array $routes)
    {
        foreach($routes as $key => $value) {
            if(is_array($value) && is_string($key) && $key[0] == '@') { // prefixed routes start with '@'
                $prefix = substr($key, 1);
                foreach($value as $regex => $action) {
                    $method = '';
                    $matches = array();
                    if(preg_match('%^([A-Za-z]+:)%', $regex, $matches)) { // This caters for specific methods.
                        $method = $matches[1];
                        $regex = substr($regex, strlen($method));
                    }
                    $this->routes[$method . $prefix . $regex] = new RouteCall($app, $action);
                }
            }
            else {
                $this->routes[$key] = new RouteCall($app, $value);
            }
        }
        
        krsort($this->routes);
        
        return $this;
    }
    
    /**
     * Searches for a route matching the provided request.
     */
    function getRoute(events\HttpEvent $request)
    {
        $path = $request->getRoute();

        $call = false;        // This will store the controller and method to call
        $matches = array();   // And this the extracted parameters.

        // First we search for specific method routes.
        $method_routes = preg_grep('%^' . $request->getMethod() . ':%i', array_keys($this->routes));
        foreach($method_routes as $route) {
            $method = $request->getMethod() . ':';
            $clean_route = substr($route, strlen($method));
            if(preg_match('%^'. $clean_route .'/?$%i', $path, $matches)) {
                $call = $this->routes[$route];
                break;
            }
        }

        // Do we need to try generic routes?
        if(!$call) {
            foreach($this->routes as $regex => $proto) {
                if(preg_match('%^'. $regex .'/?$%i', $path, $matches)) {
                    $call = $proto;
                    break;
                }
            }
        }

        // If we don't have a call at this point, that's a 404.
        if(!$call) {
            throw new \assegai\exceptions\NoRouteException(
                sprintf('URL %s not found.', $request->getWholeRoute()),
                $this->routes
            );
        }
        
        // Cleaning up the matches. The first one is always the current URL.
        $params = array_slice($matches, 1);
        
        if(is_array($call)) {
            $params = array_merge(array($call[1]), $params);
            $call = $call[0];
        }
        
        $call->setParams($params);
        
        return $call;
    }
}
