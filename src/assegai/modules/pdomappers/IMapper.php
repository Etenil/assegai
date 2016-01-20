<?php

/**
 * This file is part of Assegai
 *
 * The PDO mappers module mostly provides a mapper structure through several
 * interfaces and base classes. It is not instanciated.
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

namespace assegai\modules\pdomappers;

interface IMapper
{
    /**
     * Loads up a single object based on its primary key.
     *
     * It is recommended to utilize the loadAll() method here and extract the
     * first result. This will ensure better consistency.
     *
     * @param mixed $pk is the primary key's value to load the object for.
     */
    public function load($pk);

    /**
     * Saves the object to the database.
     * @param IObject $object is the object to be mapped and saved.
     */
    public function save(IObject $object);

    /**
     * Loads all elements compliant with a set of conditions.
     *
     * The conditions are expressed as a free-form array. It's up to you to
     * decide how you want them to be defined. Make sure you document this
     * well however.
     */
    public function loadAll(array $conditions);
}
