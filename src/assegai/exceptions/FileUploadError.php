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

namespace etenil\assegai\exceptions
{
    /**
     * The file couldn't be uploaded.
     */
    class FileUploadError extends \Exception
    {
        function __construct($error_code) {
            parent::__construct($this->_translateErrorCode($error_code), $error_code);
        }

        protected function _translateErrorCode($error_code)
        {
            switch($error_code) {
            case UPLOAD_ERR_OK:
                return 'success';
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'the uploaded file is too large';
            case UPLOAD_ERR_PARTIAL:
                return 'the file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'the file was not uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'the server has no temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'failed to write upload to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'illegal file extension';
            }
        }
    }
}
