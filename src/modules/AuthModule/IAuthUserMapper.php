<?php

namespace etenil\assegai\modules\auth
{
    interface IAuthUserMapper {
        function saveUser(IAuthUser $user);
        function loadUser($username);
        function dropUser(IAuthUser $user);
    }
}
