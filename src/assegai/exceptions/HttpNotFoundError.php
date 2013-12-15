<?php

namespace assegai\exceptions
{
    /**
     * Error 404.
     */
    class HttpNotFoundError extends HttpClientError
    {
        function __construct()
        {
            parent::__construct(404, 'Not Found');
        }
    }
}
