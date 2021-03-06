<?php

namespace TheCodingMachine\GraphQL\Controllers\Security;

use PHPUnit\Framework\TestCase;

class FailAuthenticationServiceTest extends TestCase
{

    public function testIsAllowed()
    {
        $service = new FailAuthenticationService();
        $this->expectException(SecurityNotImplementedException::class);
        $service->isLogged();
    }
}
