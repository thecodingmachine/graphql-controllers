<?php

namespace TheCodingMachine\GraphQL\Controllers;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use TheCodingMachine\GraphQL\Controllers\Fixtures\TestController;
use TheCodingMachine\GraphQL\Controllers\Security\VoidAuthenticationService;
use TheCodingMachine\GraphQL\Controllers\Security\VoidAuthorizationService;

class AggregateControllerQueryProviderTest extends AbstractQueryProviderTest
{
    public function testAggregate()
    {
        $controller = new TestController();

        $container = new class([ 'controller' => $controller ]) implements ContainerInterface {

            /**
             * @var array
             */
            private $controllers;

            public function __construct(array $controllers)
            {
                $this->controllers = $controllers;
            }

            public function get($id)
            {
                return $this->controllers[$id];
            }

            public function has($id)
            {
                return isset($this->controllers[$id]);
            }
        };

        $aggregateQueryProvider = new AggregateControllerQueryProvider([ 'controller' ], $this->getControllerQueryProviderFactory(), $this->getTypeMapper(), $container);

        $queries = $aggregateQueryProvider->getQueries();
        $this->assertCount(6, $queries);

        $mutations = $aggregateQueryProvider->getMutations();
        $this->assertCount(1, $mutations);
    }
}
