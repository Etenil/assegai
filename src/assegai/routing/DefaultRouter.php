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
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace assegai\routing
{
    use \assegai\exceptions;
    
    class DefaultRouter implements IRouter
    {
        protected $routes;
    
        /**
         * Sets the routes for this router.
         */
        function setRoutes(array $routes)
        {
            $this->routes = $routes;
        }
        
        function getRoute(\assegai\Request $request)
        {
            $path = $request->getRoute();

            $call = false;        // This will store the controller and method to call
            $matches = array();   // And this the extracted parameters.

            // First we search for specific method routes.
            $method_routes = preg_grep('/^' . $request->getMethod() . ':/i', array_keys($this->routes));
            foreach($method_routes as $route) {
                $method = $request->getMethod() . ':';
                $clean_route = substr($route, strlen($method));
                if(preg_match('%^'. $clean_route .'/?$%i',
                $path, $matches)) {
                    $call = $this->routes[$route];
                    break;
                }
            }

            // Do we need to try generic routes?
            if(!$call) {
                foreach($this->routes as $regex => $proto) {
                    if(preg_match('%^'. $regex .'/?$%i',
                    $path, $matches)) {
                        $call = $proto;
                        break;
                    }
                }
            }


            // If we don't have a call at this point, that's a 404.
            if(!$call) {
                throw new NoRouteException("URL, ".$request->getWholeRoute().", not found.");
            }
            
            return new RouteCall($call, $matches);
        }
    }
}
