<?php

namespace TheCodingMachine\GraphQL\Controllers;

use Doctrine\Common\Annotations\AnnotationReader;
use TheCodingMachine\GraphQL\Controllers\Fixtures\TestController;
use TheCodingMachine\GraphQL\Controllers\Fixtures\TestObject;
use TheCodingMachine\GraphQL\Controllers\Fixtures\TestType;
use TheCodingMachine\GraphQL\Controllers\Security\VoidAuthenticationService;
use TheCodingMachine\GraphQL\Controllers\Security\VoidAuthorizationService;
use Youshido\GraphQL\Execution\ResolveInfo;
use Youshido\GraphQL\Type\InputObject\InputObjectType;
use Youshido\GraphQL\Type\ListType\ListType;
use Youshido\GraphQL\Type\NonNullType;
use Youshido\GraphQL\Type\Scalar\BooleanType;
use Youshido\GraphQL\Type\Scalar\DateTimeType;
use Youshido\GraphQL\Type\Scalar\FloatType;
use Youshido\GraphQL\Type\Scalar\IntType;
use Youshido\GraphQL\Type\Scalar\StringType;
use TheCodingMachine\GraphQL\Controllers\Annotations\Query;

class ControllerQueryProviderTest extends AbstractQueryProviderTest
{
    public function testQueryProvider()
    {
        $controller = new TestController();
        $reader = new AnnotationReader();

        $queryProvider = new ControllerQueryProvider($controller, $reader, $this->getTypeMapper(), $this->getHydrator(), new VoidAuthenticationService(), new VoidAuthorizationService());

        $queries = $queryProvider->getQueries();

        $this->assertCount(2, $queries);
        $usersQuery = $queries[0];
        $this->assertSame('test', $usersQuery->getName());

        $this->assertCount(8, $usersQuery->getArguments());
        $this->assertInstanceOf(NonNullType::class, $usersQuery->getArgument('int')->getType());
        $this->assertInstanceOf(IntType::class, $usersQuery->getArgument('int')->getType()->getTypeOf());
        $this->assertInstanceOf(StringType::class, $usersQuery->getArgument('string')->getType());
        $this->assertInstanceOf(NonNullType::class, $usersQuery->getArgument('list')->getType());
        $this->assertInstanceOf(ListType::class, $usersQuery->getArgument('list')->getType()->getTypeOf());
        $this->assertInstanceOf(NonNullType::class, $usersQuery->getArgument('list')->getType()->getTypeOf()->getItemType());
        $this->assertInstanceOf(InputObjectType::class, $usersQuery->getArgument('list')->getType()->getTypeOf()->getItemType()->getTypeOf());
        $this->assertInstanceOf(BooleanType::class, $usersQuery->getArgument('boolean')->getType());
        $this->assertInstanceOf(FloatType::class, $usersQuery->getArgument('float')->getType());
        $this->assertInstanceOf(DateTimeType::class, $usersQuery->getArgument('dateTimeImmutable')->getType());
        $this->assertInstanceOf(DateTimeType::class, $usersQuery->getArgument('dateTime')->getType());
        $this->assertInstanceOf(StringType::class, $usersQuery->getArgument('withDefault')->getType());
        $this->assertSame('TestObject', $usersQuery->getArgument('list')->getType()->getTypeOf()->getItemType()->getTypeOf()->getName());

        $mockResolveInfo = $this->createMock(ResolveInfo::class);

        $context = ['int' => 42, 'string' => 'foo', 'list' => [
            ['test' => 42],
            ['test' => 12],
        ],
            'boolean' => true,
            'float' => 4.2,
            'dateTimeImmutable' => '2017-01-01 01:01:01',
            'dateTime' => '2017-01-01 01:01:01'
        ];

        $result = $usersQuery->resolve('foo', $context, $mockResolveInfo);

        $this->assertInstanceOf(TestObject::class, $result);
        $this->assertSame('foo424212true4.22017010101010120170101010101default', $result->getTest());

        unset($context['string']); // Testing null default value
        $result = $usersQuery->resolve('foo', $context, $mockResolveInfo);

        $this->assertSame('424212true4.22017010101010120170101010101default', $result->getTest());
    }

    public function testMutations()
    {
        $controller = new TestController();
        $reader = new AnnotationReader();

        $queryProvider = new ControllerQueryProvider($controller, $reader, $this->getTypeMapper(), $this->getHydrator(), new VoidAuthenticationService(), new VoidAuthorizationService());

        $mutations = $queryProvider->getMutations();

        $this->assertCount(1, $mutations);
        $mutation = $mutations[0];
        $this->assertSame('mutation', $mutation->getName());

        $mockResolveInfo = $this->createMock(ResolveInfo::class);

        $result = $mutation->resolve('foo', ['testObject' => ['test' => 42]], $mockResolveInfo);

        $this->assertInstanceOf(TestObject::class, $result);
        $this->assertEquals('42', $result->getTest());
    }

    public function testErrors()
    {
        $controller = new class
        {
            /**
             * @Query
             * @return string
             */
            public function test($noTypeHint): string
            {
                return 'foo';
            }
        };
        $reader = new AnnotationReader();

        $queryProvider = new ControllerQueryProvider($controller, $reader, $this->getTypeMapper(), $this->getHydrator(), new VoidAuthenticationService(), new VoidAuthorizationService());

        $this->expectException(MissingTypeHintException::class);
        $queryProvider->getQueries();
    }

    public function testQueryProviderWithFixedReturnType()
    {
        $controller = new TestController();
        $reader = new AnnotationReader();

        $queryProvider = new ControllerQueryProvider($controller, $reader, $this->getTypeMapper(), $this->getHydrator(), new VoidAuthenticationService(), new VoidAuthorizationService());

        $queries = $queryProvider->getQueries();

        $this->assertCount(2, $queries);
        $fixedQuery = $queries[1];

        $this->assertInstanceOf(TestType::class, $fixedQuery->getType());
    }
}
