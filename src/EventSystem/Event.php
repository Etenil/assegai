<?php

/*
 * Copyright (C) 2015  Guillaume Pasquet
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
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

namespace Assegai\EventSystem;

/**
 * The basic representation of an Event. This class could almost be abstract,
 * but a generic type of event can be useful.
 */
class Event {
    /** @var array An array of event parameters. */
    protected $params;

    /** @var string the name of this particular type of event. */
    protected $type = 'generic';

    public function __construct(array $params = null) {
        if(null === $params) {
            $params = array();
        }
        $this->setParams($params);
    }
    
    public function setParams(array $params) {
        $this->params = $params;
        return $this;
    }
    
    public function getParams() {
        return $this->params;
    }
}
