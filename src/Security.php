<?php

namespace SecureMy;


class Security
{
    const IDENTIFIER_REGEX = '/^[a-z0-9._ ]+$/i';

    public static function validateIdentifier($identifier)
    {
         if(!preg_match(self::IDENTIFIER_REGEX, $identifier)) {
             throw new \InvalidArgumentException("'$identifier' is not a valid identifier");
         }
    }
}
