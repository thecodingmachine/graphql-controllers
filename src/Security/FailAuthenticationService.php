<?php


namespace TheCodingMachine\GraphQL\Controllers\Security;

/**
 * A fake authentication service that always returns that the current user is NOT authenticated.
 */
class FailAuthenticationService implements AuthenticationServiceInterface
{

    /**
     * Returns true if the "current" user is logged
     *
     * @return bool
     */
    public function isLogged(): bool
    {
        throw SecurityNotImplementedException::createNoAuthenticationException();
    }
}
