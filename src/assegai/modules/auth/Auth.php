<?php

namespace assegai\modules\auth
{

    /**
     * Auth module for Assegai
     */
    class Auth extends \assegai\Module
    {
        protected $user_model;
        protected $group_model;
        protected $user_mapper;
        protected $group_mapper;

        protected $hash_method = 'sha256';

        protected $users;
        protected $groups;

        public $user; // Currently logged-in user.

        public function _init($options)
        {
            if(!isset($options['user']) || !isset($options['group'])) {
                throw new \Exception('You must specify the User and Group models to use the Auth module.');
            }

            $this->user_model = $options['user'];
            $this->group_model = $options['group'];

            if(isset($options['usermapper'])) {
                $this->user_mapper = $options['usermapper'];
            } else {
                $this->user_mapper = $this->user_model . 'Mapper';
            }

            if(isset($options['groupmapper'])) {
                $this->group_mapper = $options['groupmapper'];
            } else {
                $this->group_mapper = $this->group_model . 'Mapper';
            }

            $this->users = $this->model($this->user_mapper);
            $this->groups = $this->model($this->group_mapper);
        }

        public static function instanciate()
        {
            return true;
        }

        protected function hash($data) {
            if(function_exists('password_hash')) {
                return password_hash($data, PASSWORD_DEFAULT);
            } else {
                return hash($this->hash_method, $data);
            }
        }

        protected function checkhash($data, $hash) {
            if(function_exists('password_verify')) {
                return password_verify($data, $hash);
            } else {
                return ($this->hash($data) == $hash);
            }
        }

        public function register($username, $password, $email, $firstname = '', $lastname = '') {
            $user = new $this->user_model();
            $user->setUsername($username);
            $user->setPasswordHash($this->hash($password));
            $user->setEmail($email);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);

            $this->users->saveUser($user);

            return $user;
        }

        public function authenticate($username, $password) {
            $user = $this->users->loadUser($username);
            if($this->checkhash($password, $user->getPasswordHash())) {
                return $user;
            } else {
                return false;
            }
        }

        public function login(\assegai\Request $request, $user) {
            $request->setSession('auth_username', $user->getUsername());
            $this->user = $user;
        }

        public function logout(\assegai\Request $request) {
            $request->setSession('auth_username', null);
        }

        public function preProcess($request, $proto) {
            $username = $request->getSession('auth_username');
            if($username) {
                $this->user = $this->users->loadUser($username);
            }
        }
    }
}
