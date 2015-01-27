<?php

namespace etenil\assegai\modules\mail
{
    /**
     * Email service provider.
     */
    interface Service
    {
        function send(Email $email);
    }
}
