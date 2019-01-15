<?php

namespace TheCodingMachine\GraphQL\Controllers\Cache;

use function clearstatcache;
use PHPUnit\Framework\TestCase;
use function stat;
use function strtotime;
use function sys_get_temp_dir;
use function time;
use function touch;
use function unlink;

class FileModificationTimeCacheValidatorTraitTest extends TestCase
{

    public function testIsValid()
    {
        $cacheItem = new class implements CacheValidatorInterface {
            use FileModificationTimeCacheValidatorTrait;
        };

        $file = sys_get_temp_dir().'/test_file_graphql_controllers';
        touch($file, strtotime('2019-01-01'));
        $cacheItem->addTrackedFile($file);

        $this->assertTrue($cacheItem->isValid());

        touch($file, strtotime('2019-01-02'));
        clearstatcache($file);
        $this->assertFalse($cacheItem->isValid());
        unlink($file);
    }
}
