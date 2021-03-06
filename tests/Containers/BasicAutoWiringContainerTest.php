<?php

namespace TheCodingMachine\GraphQL\Controllers\Containers;

use GraphQL\Type\Definition\ObjectType;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TheCodingMachine\GraphQL\Controllers\AbstractQueryProviderTest;
use TheCodingMachine\GraphQL\Controllers\Fixtures\TestType;
use TheCodingMachine\GraphQL\Controllers\Security\AuthorizationServiceInterface;

class BasicAutoWiringContainerTest extends AbstractQueryProviderTest
{
    private function getContainer(): ContainerInterface
    {
        return new class implements ContainerInterface {
            public function get($id)
            {
                return 'foo';
            }

            public function has($id)
            {
                return $id === 'foo';
            }
        };
    }

    public function testFromContainer()
    {
        $container = $this->buildAutoWiringContainer($this->getContainer());

        $this->assertTrue($container->has('foo'));
        $this->assertFalse($container->has('bar'));

        $this->assertSame('foo', $container->get('foo'));
    }

    public function testInstantiate()
    {
        $container = $this->buildAutoWiringContainer($this->getContainer());

        $this->assertTrue($container->has(TestType::class));
        $type = $container->get(TestType::class);
        $this->assertInstanceOf(TestType::class, $type);
        $this->assertSame($type, $container->get(TestType::class));
        $this->assertTrue($container->has(TestType::class));
    }

    public function testNotFound()
    {
        $container = $this->buildAutoWiringContainer($this->getContainer());
        $this->expectException(NotFoundException::class);
        $container->get('notfound');
    }
}
