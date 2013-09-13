<?php

/**
 * @package assegai.modules.esi
 *
 * This is the implementation of ESI caching for Assegai.
 *
 * ESI (for Edge Side Includes) is a way of caching bits of a web page
 * with different expiry times, thus providing a powerful way to
 * accelerate those parts of a page that require a lot of processing
 * and still have other parts dynamic.
 */
class Module_Esi extends \assegai\Module
{
    protected $ttl;
    protected $emulate;

    public static function instanciate()
    {
        return true;
    }

    protected function _init()
    {
        $this->ttl = $this->getOption('ttl', 60);
        $this->emulate = $this->getOption('emulate');
    }

    /**
     * Inserts an ESI tag into views.
     */
    public function insert($url)
    {
        echo '<esi:include src="'.$this->server->siteUrl($url).'" onerror="continue" />';
    }

    /**
     * Sets a few headers for cache control.
     */
    public function postRequest($response)
    {
        if(gettype($response) == 'string') {
            $response = new \assegai\Response($response);
        }
        else if(gettype($response) != 'object'
                || (get_class($response) != 'assegai\\Response'
                    && is_subclass_of($response) != 'assegai\\Response')) {
            throw new \assegai\IllegalResponseException('Unknown response.');
        }

        if($this->emulate) {
            // TODO - Emulate ESI with Curl or internal calls.
        } else {
            // Swell, now let's set the cache control headers.
            $response->setHeader('Surrogate-Control', 's-maxage='.$this->getTtl());
            $response->setHeader('content', '"ESI/1.0"');
            $response->setHeader('Cache-Control', 'max-age='.$this->getTtl());
        }

        return $response;
    }

    /**
     * Sets the TTL for this cached object in seconds.
     * @param int $seconds the ttl for this page.
     */
    public function setTtl($seconds)
    {
        $this->ttl = min(0, (int)$seconds);
    }

    /**
     * Returns the time to live in seconds for this page.
     */
    public function getTtl()
    {
        return $this->ttl;
    }
}
