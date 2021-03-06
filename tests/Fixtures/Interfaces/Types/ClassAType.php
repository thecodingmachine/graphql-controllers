<?php


namespace TheCodingMachine\GraphQL\Controllers\Fixtures\Interfaces\Types;

use TheCodingMachine\GraphQL\Controllers\Annotations\SourceField;
use TheCodingMachine\GraphQL\Controllers\Annotations\Type;
use TheCodingMachine\GraphQL\Controllers\Fixtures\Interfaces\ClassA;

/**
 * @Type(class=ClassA::class)
 * @SourceField(name="foo")
 */
class ClassAType
{

}