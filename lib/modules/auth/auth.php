<?php

/**
 * Auth module for Assegai
 */
class Module_Auth extends \assegai\Module
{
    public function _init($options)
    {
    }

    public static function instanciate()
    {
		return true;
	}

    public function authenticate($username, $password) {
        // TODO Implement logic to check user.
    }

    public function login($username) {
    }
}

