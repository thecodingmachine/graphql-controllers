<?php


namespace TheCodingMachine\GraphQL\Controllers\Security;


class SecurityNotImplementedException extends \LogicException
{

    public static function createNoAuthenticationException(): self
    {
        return new self('GraphQL-Controllers does not know how to check for authentication. You probably tried to use the @Logged annotation without configuring first an AuthenticationService.');
    }

    public static function createNoAuthorizationException(): self
    {
        return new self('GraphQL-Controllers does not know how to check for authorization. You probably tried to use the @Right annotation without configuring first an AuthorizationService.');
    }
}
