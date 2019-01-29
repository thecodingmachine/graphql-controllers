<?php

namespace TheCodingMachine\GraphQL\Controllers\Schema;


/**
 * Object that can be created by a factory.
 */
interface DescriptorInterface
{
    /**
     * Returns the name of the factory capable of building this descriptor.
     *
     * @return string
     */
    public function getFactory(): string;
}