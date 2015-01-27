<?php

namespace etenil\assegai\modules\auth
{
    interface IAuthGroup {
        function getName();
        function setName($name);
    }
}
