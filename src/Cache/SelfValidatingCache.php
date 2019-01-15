<?php


namespace TheCodingMachine\GraphQL\Controllers\Cache;


use Psr\SimpleCache\CacheInterface;

class SelfValidatingCache implements SelfValidatingCacheInterface
{

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key The unique key of this item in the cache.
     *
     * @return mixed The value of the item from the cache, or null in case of cache miss.
     */
    public function get(string $key)
    {
        $item = $this->cache->get($key);
        if (!$item instanceof CacheValidatorInterface) {
            throw InvalidCacheItemException::create($key);
        }
        return $item;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key The key of the item to store.
     * @param mixed $value The value of the item to store, must be serializable.
     *
     */
    public function set(string $key, $value)
    {
        if (!$value instanceof CacheValidatorInterface) {
            throw InvalidCacheItemException::create($key);
        }
        $this->cache->set($key, $value);
    }
}
