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
}
