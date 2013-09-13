<?php

/**
 * Multiple exceptions for assegai.
 *
 * @package exceptions
 *
 * @copyright
 * This file is part of assegai.
 *
 * assegai is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * assegai is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with assegai.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace assegai;

class HTTPStatus extends \Exception
{
    // Error code becomes mandatory and let's reorder them.
    function __construct($status_code, $description)
    {
        parent::__construct($description, $status_code);
    }
}

/**
 * Redirect (probably the most used exception).
 */
class HTTPRedirect extends HTTPStatus
{
    /** The URL to redirect to. */
    protected $url;

    /**
     * Redirects the visitor to some URL.
     * @param string $url is the URL to send the visitor to.
     */
    public function __construct($url, $code = 301)
    {
        $this->code = $code;
        $this->url = $url;
    }

    /**
     * Gets the url to redirect to.
     */
    public function getUrl()
    {
        return $this->url;
    }
}

class HTTPSuccess extends HTTPStatus
{}

/**
 * Client-side HTTP error.
 */
class HTTPClientError extends HTTPStatus
{}

/**
 * Server-side HTTP error.
 */
class HTTPServerError extends HTTPStatus
{}

/**
 * Error 404.
 */
class HTTPNotFoundError extends HTTPClientError
{
    function __construct()
    {
        parent::__construct(404, 'Not Found');
    }
}

/**
 * 500 error.
 */
class HTTPInternalServerError extends HTTPServerError
{
    function __construct()
    {
        parent::__construct(500, 'Server Error');
    }
}

/**
 * Exception for a route that doesn't exist.
 */
class NoRouteException extends HTTPNotFoundError
{}

/**
 * No handler to a route.
 */
class NoHandlerException extends HTTPInternalServerError
{}

/**
 * View doesn't exist, file not found.
 */
class NoViewException extends HTTPNotFoundError
{}

/**
 * The object returned by a controller cannot be converted to a
 * Response.
 */
class IllegalResponseException extends HTTPInternalServerError
{}

/**
 * Pre-empts execution of other modules.
 */
class ModulePreemptException extends \Exception
{}

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
