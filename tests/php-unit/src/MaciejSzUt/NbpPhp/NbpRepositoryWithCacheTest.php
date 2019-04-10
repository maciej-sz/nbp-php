<?php
namespace MaciejSzUt\NbpPhp;

use Doctrine\Common\Cache\FilesystemCache;
use MaciejSz\NbpPhp\Service\NbpCache;

class NbpRepositoryWithCacheTest extends NbpRepositoryTest
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $CacheBackend = new FilesystemCache(sys_get_temp_dir() . "/nbp-php");
        self::$_NbpCache = new NbpCache($CacheBackend);
    }
}
 