<?php

namespace etenil\assegai\modules\auth
{
    interface IAuthGroupMapper {
        function saveGroup(IAuthGroup $group);
        function loadGroup($name);
        function dropGroup(IAuthGroup $group);
    }
}
