<?php

namespace etenil\modules\AuthModule\models

interface IAuthGroupMapper
{
    function saveGroup(IAuthGroup $group);
    function loadGroup($name);
    function dropGroup(IAuthGroup $group);
}
