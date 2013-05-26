<?php

namespace assegai;

/**
 * Configuration class, a read-only dictionary.
 */
class Config
{
    protected $settings;

    public static function fromArray(array $definitions)
    {
        return new self($definitions);
    }

    public static function fromFile($path)
    {
        if(!file_exists($path)) {
            throw new Exception("File `$path' doesn't exist.");
        }

        $conf = array();
        require($path);

        return self::fromArray($conf);
    }

    public function addArray(array $definitions)
    {
    }

    public function addFile($path)
    {
    }

    protected function __construct(array $definitions)
    {
        $this->settings = $definitions;
    }

    public function get($defname)
    {
        if(isset($this->settings[$defname])) {
            return $this->settings[$defname];
        } else {
            return null;
        }
    }

    public function getAll()
    {
        return $this->settings;
    }
}
