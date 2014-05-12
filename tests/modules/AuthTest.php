<?php

require('../lib/modules/auth/auth.php');

class AuthTestUserModel implements IAuthUser {
    function getUsername() { return $this->username; }
    function setUsername($username) { $this->username = $username; }
    function getPasswordHash() { return $this->password; }
    function setPasswordHash($hash) { $this->password = $hash; }
    function getEmail() { return $this->email; } 
    function setEmail($email) { $this->email = $email; }
    function getFirstname() { return $this->firstname; }
    function setFirstname($firstname) { $this->firstname = $firstname; }
    function getLastname() { return $this->lastname; }
    function setLastname($lastname) { $this->lastname = $lastname; }
    function getGroup() { return $this->group; }
    function setGroup(IAuthGroup $group) { $this->group = $group; }
}

class AuthTestGroupModel implements IAuthGroup {
    function getName() { return $this->name; }
    function setName($name) { $this->name = $name; }
}

class AuthTestUserMapper implements IAuthUserMapper {
    static $users = array();
    function saveUser(IAuthUser $user) { self::$users[$user->getUsername()] = $user; }
    function loadUser($username) { return self::$users[$username]; }
    function dropUser(IAuthUser $user) { unset(self::$users[$user->getUsername()]); }
}

class AuthTestGroupMapper implements IAuthGroupMapper {
    static $groups = array();
    function saveGroup(IAuthGroup $group) { self::$groups[$group->getName()] = $group; }
    function loadGroup($name) { return self::$groups[$name]; }
    function dropGroup(IAuthGroup $group) { unset(self::$groups[$group->getName()]); }
}

class AuthTest extends \centrifuge\Test
{
    protected $auth;

    function init() {
        $this->auth = new Module_Auth(new \assegai\Server(array()), array(
            'user' => 'AuthTestUserModel',
            'group' => 'AuthTestGroupModel',
            'usermapper' => 'AuthTestUserMapper',
            'groupmapper' => 'AuthTestGroupMapper',
        ));
    }

    function testRegister() {
        $user = $this->auth->register('bob', 'foobar', 'bob@foobar.com', 'Bob', 'Smith');
        $this->assert(isset(AuthTestUserMapper::$users['bob']));
        $suser = AuthTestUserMapper::$users['bob'];
        $this->equals('bob', $suser->getUsername());
        $this->equals('bob@foobar.com', $suser->getEmail());
        $this->equals('Bob', $suser->getFirstname());
        $this->equals('Smith', $suser->getLastname());
    }

    function testAuthenticate() {
        $this->assert($this->auth->authenticate('bob', 'foobar'));
        $this->nassert($this->auth->authenticate('bob', 'barbaz'));
    }
}

