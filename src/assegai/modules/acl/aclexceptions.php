<?php

namespace assegai\modules\acl
{
    /**
     * @package assegai.modules.acl
     *
     * Exceptions for the ACL module.
     */
    class UndefinedRoleException extends \Exception
    {}

    class UndefinedResourceException extends \Exception
    {}

    class AclTreeParseException extends \Exception
    {}
}