<?php

/**
 * The basic implementation of a Model.
 *
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
 
namespace assegai;
 
class Model
{
    /** Modules that were loaded by the application. */
    protected $modules;

    /**
     * Instanciates the model.
     * @param ModuleContainer $modules is a container of modules
     * instanciated by the application.
     */
    public function __construct(modules\ModuleContainer $modules)
    {
        $this->modules = $modules;
        $this->_init();
    }

    /**
     * Method to be implemented by the user that is called at the end
     * of the constructor.. Avoids having to overload the constructor
     * and care about the parent constructor's logic.
     */
    protected function _init()
    {
    }

    /**
     * Easy loading of another module.
     */
    protected function model($model_name)
    {
        if(!class_exists($model_name)) {
            throw new exceptions\HttpInternalServerError("Class $model_name not found");
        }
        
        return new $model_name($this->modules);
    }
}

?>