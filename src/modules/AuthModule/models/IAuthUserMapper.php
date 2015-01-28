<?php

namespace etenil\modules\AuthModule\models;

interface IAuthUserMapper
{
    function saveUser(IAuthUser $user);
    function loadUser($username);
    function dropUser(IAuthUser $user);
}
