<?php


namespace TheCodingMachine\GraphQL\Controllers\Cache;


class InvalidCacheItemException extends \Exception
{
    public static function create(string $name): self
    {
        return new self(sprintf('Cache item "%s" does not implement the SelfValidatingCacheInterface.', $name));
    }
}