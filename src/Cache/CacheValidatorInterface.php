<?php


namespace TheCodingMachine\GraphQL\Controllers\Cache;

/**
 * An interface in charge of checking if some item coming out of cache is still valid.
 *
 * Useful for items depending on some file timestamp for instance.
 */
interface CacheValidatorInterface
{
    /**
     * Returns true if this item is valid (coming out of cache), false otherwise.
     *
     * @return bool
     */
    public function isValid(): bool;
}
