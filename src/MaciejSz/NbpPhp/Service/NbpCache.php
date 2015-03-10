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
     * @param string $url
     * @return null|array
     */
    public function tryGet($url)
    {
        if ( isset(self::$_front_cache[$url]) ) {
            return self::$_front_cache[$url];
        }
        if ( !$this->_Cache ) {
            return null;
        }
        $dir = $this->_Cache->fetch($url);
        if ( false === $dir ) {
            return null;
        }
        self::$_front_cache[$url] = $dir;
        return $dir;
    }

    /**
     * @param string $url
     * @param array $data
     * @return $this
     */
    public function set($url, array $data)
    {
        self::$_front_cache[$url] = $data;
        if ( $this->_Cache ) {
            $this->_Cache->save($url, $data, $this->getLifeTime());
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
 