<?php
namespace MaciejSz\NbpPhp\Service;
 
use Doctrine\Common\Cache\Cache;

class NbpCache
{
    const DEFAULT_LIFE_TIME = 86400; // 1d

    /**
     * @var null|Cache
     */
    protected $_Cache = null;

    /**
     * @var null|int
     */
    private $_life_time = null;

    /**
     * @var array
     */
    private static $_front_cache = [];

    /**
     * @var null|NbpCache
     */
    private static $_DefaultInstance = null;

    /**
     * @param null|Cache $Cache [optional]
     * @param null|int $life_time [optional]
     */
    public function __construct(Cache $Cache = null, $life_time = null)
    {
        $this->_Cache = $Cache;
        $this->_life_time = $life_time;
    }

    /**
     * @param NbpCache $NbpCache
     * @return NbpCache
     */
    public static function ensureInstance(NbpCache $NbpCache = null)
    {
        if ( null === $NbpCache ) {
            $NbpCache = self::defaultInstance();
        }
        return $NbpCache;
    }

    /**
     * @return NbpCache
     */
    public static function defaultInstance()
    {
        if ( null === self::$_DefaultInstance ) {
            self::$_DefaultInstance = new self();
        }
        return self::$_DefaultInstance;
    }

    /**
     * @param string $year
     * @return string
     */
    public function buildCacheKey($year)
    {
        return self::class . "\\{$year}";
    }

    /**
     * @param string $year
     * @return null|array
     */
    public function tryGet($year)
    {
        $key = $this->buildCacheKey($year);
        if ( isset(self::$_front_cache[$key]) ) {
            return self::$_front_cache[$key];
        }
        if ( !$this->_Cache ) {
            return null;
        }
        $dir = $this->_Cache->fetch($key);
        if ( false === $dir ) {
            return null;
        }
        self::$_front_cache[$key] = $dir;
        return $dir;
    }

    /**
     * @param string $year
     * @param array $data
     * @return $this
     */
    public function set($year, array $data)
    {
        $key = $this->buildCacheKey($year);
        self::$_front_cache[$key] = $data;
        if ( $this->_Cache ) {
            $this->_Cache->save($key, $data, $this->getLifeTime());
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getLifeTime()
    {
        if ( null === $this->_life_time ) {
            $this->_life_time = self::DEFAULT_LIFE_TIME;
        }
        return $this->_life_time;
    }
}
 