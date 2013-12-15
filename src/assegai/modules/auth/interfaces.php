<?php

namespace assegai\modules\auth
{
    interface IAuthUser {
        function getUsername();
        function setUsername($username);
        function getPasswordHash();
        function setPasswordHash($hash);
        function getEmail();
        function setEmail($email);
        function getFirstname();
        function setFirstname($firstname);
        function getLastname();
        function setLastname($lastname);
        function getGroup();
        function setGroup(IAuthGroup $group);
    }

    interface IAuthUserMapper {
        function saveUser(IAuthUser $user);
        function loadUser($username);
        function dropUser(IAuthUser $user);
    }

    interface IAuthGroup {
        function getName();
        function setName($name);
    }

    interface IAuthGroupMapper {
        function saveGroup(IAuthGroup $group);
        function loadGroup($name);
        function dropGroup(IAuthGroup $group);
    }
}
