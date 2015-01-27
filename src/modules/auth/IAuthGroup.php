<?php

namespace assegai\modules\auth
{
    interface IAuthGroup {
        function getName();
        function setName($name);
    }
}
