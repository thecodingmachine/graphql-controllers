<?php


namespace TheCodingMachine\GraphQL\Controllers\Cache;

/**
 * A cache service that can itself invalidate cache items.
 */
interface SelfValidatingCacheInterface
{
    /**
     * Fetches a value from the cache.
     *
     * @param string $key     The unique key of this item in the cache.
     *
     * @return mixed The value of the item from the cache, or null in case of cache miss.
     */
    public function get(string $key);

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string                 $key   The key of the item to store.
     * @param mixed                  $value The value of the item to store, must be serializable.
     *
     */
    public function set(string $key, $value);
}
