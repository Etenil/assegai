<?php

namespace assegai\exceptions
{
    /**
     * 500 error.
     */
    class HttpInternalServerError extends HttpServerError
    {
        function __construct()
        {
            parent::__construct(500, 'Server Error');
        }
    }
}
