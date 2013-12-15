<?php

namespace assegai\exceptions
{
    /**
     * The object returned by a controller cannot be converted to a
     * Response.
     */
    class IllegalResponseException extends HttpInternalServerError
    {
    }
}
