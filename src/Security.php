<?php

namespace SecureMy;


class Security
{
    const IDENTIFIER_REGEX = '/^[a-z0-9._ ]+$/i';
    const FUNC_REGEX = '/^[a-z0-9_]+$/i';


    public static function validateIdentifier($identifier)
    {
         if(!preg_match(self::IDENTIFIER_REGEX, $identifier)) {
             throw new \InvalidArgumentException("'$identifier' is not a valid identifier");
         }
    }

    public static function validateFuncName($funcName)
    {
        if(!preg_match(self::IDENTIFIER_REGEX, $funcName)) {
            throw new \InvalidArgumentException("'$funcName' is not a valid function name");
        }
    }
}
