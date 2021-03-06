<?php

namespace TheCodingMachine\GraphQL\Controllers;

use ReflectionMethod;
use TheCodingMachine\GraphQL\Controllers\Fixtures\TestObject;

class InputTypeUtilsTest extends AbstractQueryProviderTest
{

    public function testNoReturnType()
    {
        $inputTypeGenerator = $this->getInputTypeUtils();

        $this->expectException(MissingTypeHintException::class);
        $this->expectExceptionMessage('Factory "TheCodingMachine\\GraphQL\\Controllers\\InputTypeUtilsTest::factoryNoReturnType" must have a return type.');
        $inputTypeGenerator->getInputTypeNameAndClassName(new ReflectionMethod($this, 'factoryNoReturnType'));
    }

    public function testInvalidReturnType()
    {
        $inputTypeGenerator = $this->getInputTypeUtils();

        $this->expectException(MissingTypeHintException::class);
        $this->expectExceptionMessage('The return type of factory "TheCodingMachine\\GraphQL\\Controllers\\InputTypeUtilsTest::factoryStringReturnType" must be an object, "string" passed instead.');
        $inputTypeGenerator->getInputTypeNameAndClassName(new ReflectionMethod($this, 'factoryStringReturnType'));
    }

    public function testNullableReturnType()
    {
        $inputTypeGenerator = $this->getInputTypeUtils();

        $this->expectException(MissingTypeHintException::class);
        $this->expectExceptionMessage('Factory "TheCodingMachine\\GraphQL\\Controllers\\InputTypeUtilsTest::factoryNullableReturnType" must have a non nullable return type.');
        $inputTypeGenerator->getInputTypeNameAndClassName(new ReflectionMethod($this, 'factoryNullableReturnType'));
    }

    public function factoryNoReturnType()
    {
        
    }

    public function factoryStringReturnType(): string
    {
        return '';
    }

    public function factoryNullableReturnType(): ?TestObject
    {
        return null;
    }

}
