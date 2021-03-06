<?php

namespace TheCodingMachine\GraphQL\Controllers;

use Mouf\Picotainer\Picotainer;
use stdClass;
use TheCodingMachine\GraphQL\Controllers\Fixtures\TypeFoo;
use TheCodingMachine\GraphQL\Controllers\Types\MutableObjectType;

class TypeGeneratorTest extends AbstractQueryProviderTest
{
    private $container;

    public function setUp()
    {
        $this->container = new Picotainer([
            TypeFoo::class => function() { return new TypeFoo(); },
            stdClass::class => function() { return new stdClass(); }
        ]);
    }

    public function testNameAndFields()
    {
        $typeGenerator = $this->getTypeGenerator();

        $type = $typeGenerator->mapAnnotatedObject(TypeFoo::class, $this->getTypeMapper(), $this->container);

        $this->assertSame('TestObject', $type->name);
        $type->freeze();
        $this->assertCount(1, $type->getFields());
    }

    public function testMapAnnotatedObjectException()
    {
        $typeGenerator = $this->getTypeGenerator();

        $this->expectException(MissingAnnotationException::class);
        $typeGenerator->mapAnnotatedObject(stdClass::class, $this->getTypeMapper(), $this->container);
    }

    public function testextendAnnotatedObjectException()
    {
        $typeGenerator = $this->getTypeGenerator();

        $type = new MutableObjectType([
            'name' => 'foo',
            'fields' => []
        ]);

        $this->expectException(MissingAnnotationException::class);
        $typeGenerator->extendAnnotatedObject(new stdClass(), $type, $this->getTypeMapper());
    }
}
