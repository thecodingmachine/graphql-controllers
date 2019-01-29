<?php

namespace TheCodingMachine\GraphQL\Controllers\Schema\ObjectType;


/**
 * A serializable description of an object type.
 */
interface ObjectTypeDescriptorInterface
{
    /**
     * Returns the GraphQL name of this type.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the fully qualified PHP class name we are mapping to.
     *
     * @return string
     */
    public function getClass(): string;

    /**
     * Returns an array of interface descriptors, mapped by name.
     *
     * @return array<string, InterfaceDescriptor>
     */
    public function getInterfaces(): array;

    /**
     * Returns an array of field descriptors, mapped by name.
     *
     * @return array<string, FieldDescriptor>
     */
    public function getFields(): array;
}
