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
    class RouteCall
    {
        protected $app;
        protected $call;
        protected $params;
        
        /**
         * Creates the RouteCall object.
         * @param $app string is the routing app's name
         * @param $call callable is a method call
         * @param $params array are parameters extracted from the route to be passed on to the callback.
         */
        function __construct($app, $call, array $params = array())
        {
            $this->app = $app;
            $this->call = $call;
            $this->params = $params;
        }
        
        function getApp()
        {
            return $this->app;
        }
        
        function getCall()
        {
            return $this->call;
        }
        
        function setParams(array $params)
        {
            $this->params = $params;
            return $this;
        }
        
        function getParams()
        {
            return $this->params;
        }
    }
}
