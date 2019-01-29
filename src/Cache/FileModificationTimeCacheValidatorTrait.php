<?php


namespace TheCodingMachine\GraphQL\Controllers\Cache;

use function filemtime;

/**
 * A trait that validates a cache item based on a file modification time
 */
trait FileModificationTimeCacheValidatorTrait
{
    /**
     * An array mapping the file name to its latest modification date.
     *
     * @var array<string, int>
     */
    protected $trackedFiles = [];

    /**
     * Adds a file to track.
     * All files must not have changed for the cache to be valid.
     *
     * @param string $fileName
     */
    public function addTrackedFile(string $fileName): void
    {
        $this->trackedFiles[$fileName] = filemtime($fileName);
    }

    /**
     * Returns true if this item is valid (coming out of cache), false otherwise.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        foreach ($this->trackedFiles as $fileName => $mtime) {
            if (filemtime($fileName) !== $mtime) {
                return false;
            }
        }
        return true;
    }
}
