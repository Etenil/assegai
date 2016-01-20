<?php

namespace assegai\modules\auth;

interface IAuthGroupMapper
{
    public function saveGroup(IAuthGroup $group);
    public function loadGroup($name);
    public function dropGroup(IAuthGroup $group);
}
