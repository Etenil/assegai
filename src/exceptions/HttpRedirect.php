<?php

namespace etenil\assegai\exceptions
{
    /**
     * Redirect (probably the most used exception).
     */
    class HttpRedirect extends HttpStatus
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
}
