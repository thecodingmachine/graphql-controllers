<?php


namespace TheCodingMachine\GraphQL\Controllers\Schema\ObjectType;

use TheCodingMachine\GraphQL\Controllers\Cache\CacheValidatorInterface;
use TheCodingMachine\GraphQL\Controllers\Cache\FileModificationTimeCacheValidatorTrait;
use TheCodingMachine\GraphQL\Controllers\Schema\DescriptorInterface;

/**
 * A serializable description of an object type.
 */
class ObjectTypeDescriptor implements CacheValidatorInterface, ObjectTypeDescriptorInterface, DescriptorInterface
{
    use FileModificationTimeCacheValidatorTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * Fully qualified PHP class name we are mapping to
     *
     * @var string
     */
    private $mappedClass;
    /**
     * @var callable
     */
    private $fieldsBuilder;
    /**
     * @var callable
     */
    private $interfacesBuilder;
    /**
     * @var array<string, FieldDescriptor>
     */
    private $fields;
    /**
     * @var array<string, InterfaceDescriptor>
     */
    private $interfaces;

    /**
     * @param string $name
     * @param string $mappedClass
     */
    public function __construct(string $name, string $mappedClass, callable $fieldsBuilder, callable $interfacesBuilder)
    {
        $this->name = $name;
        $this->mappedClass = $mappedClass;
        $this->fieldsBuilder = $fieldsBuilder;
        $this->interfacesBuilder = $interfacesBuilder;
    }


    /**
     * Returns the GraphQL name of this type.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the fully qualified PHP class name we are mapping to.
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->mappedClass;
    }

    /**
     * Returns an array of interface descriptors, mapped by name.
     *
     * @return array<string, InterfaceDescriptor>
     */
    public function getInterfaces(): array
    {
        if ($this->interfaces === null) {
            $InterfaceBuilder = $this->interfacesBuilder;
            $this->interfaces = $InterfaceBuilder();
        }
        return $this->interfaces;
    }

    /**
     * Returns an array of field descriptors, mapped by name.
     *
     * @return array<string, FieldDescriptor>
     */
    public function getFields(): array
    {
        if ($this->fields === null) {
            $fieldsBuilder = $this->fieldsBuilder;
            $this->fields = $fieldsBuilder();
        }
        return $this->fields;
    }

    /**
     * @param callable $fieldsBuilder
     */
    public function addFields(callable $fieldsBuilder)
    {
        $oldFieldsBuilder = $this->fieldsBuilder;
        $this->fieldsBuilder = function() use ($oldFieldsBuilder, $fieldsBuilder) {
            return $oldFieldsBuilder() + $fieldsBuilder();
        };
    }

    /**
     * Returns the name of the factory capable of building this descriptor.
     *
     * @return string
     */
    public function getFactory(): string
    {
        return 'object_type';
    }
}
