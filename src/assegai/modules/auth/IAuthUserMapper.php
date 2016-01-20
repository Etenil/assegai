<?php

namespace assegai\modules\auth;

interface IAuthUserMapper
{
    public function saveUser(IAuthUser $user);
    public function loadUser($username);
    public function dropUser(IAuthUser $user);
}
