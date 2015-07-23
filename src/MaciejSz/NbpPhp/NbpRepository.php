<?php
namespace MaciejSz\NbpPhp;

use Doctrine\Common\Cache\FilesystemCache;
use MaciejSz\NbpPhp\Service\NbpCache;
use MaciejSz\NbpPhp\Service\NbpFileLoader;
use stdClass as StdClass;

class NbpRepository
{
    const DIR_URL = 'http://www.nbp.pl/kursy/xml/dir.txt';

    const XML_URL_PATTERN = 'http://www.nbp.pl/kursy/xml/%s.xml';

    /**
     * @var Service\NbpCache
     */
    protected $_NbpCache = null;

    /**
     * @var null|NbpFileLoader
     */
    private $_NbpFileLoader = null;

    /**
     * @var null|NbpRepository
     */
    private static $_DefaultCacheInstance = null;

    /**
     * @param Service\NbpCache $NbpCache
     */
    public final function __construct(Service\NbpCache $NbpCache = null)
    {
        $this->_NbpCache = Service\NbpCache::ensureInstance($NbpCache);
    }

    /**
     * @return NbpRepository
     */
    public static function defaultCacheInstance()
    {
        if ( null === self::$_DefaultCacheInstance ) {
            $CacheBackend = new FilesystemCache(sys_get_temp_dir() . "/nbp-php");
            $NbpCache = new NbpCache($CacheBackend);
            self::$_DefaultCacheInstance = new self($NbpCache);
        }
        return self::$_DefaultCacheInstance;
    }

    /**
     * @param string $date_str
     * @param string $type
     * @return string
     */
    public function getFileName($date_str, $type = 'a')
    {
        $dStr = Service\NbpDateStringFormatter::format($date_str);
        $entry = $this->_doGetFileName($dStr, $type);
        return $entry;
    }

    /**
     * @param string $date_str
     * @param string $type
     * @throws Exc\ENbpEntryNotFound
     * @return string
     */
    public function getFileNameBefore($date_str, $type = 'a')
    {
        $dStr = Service\NbpDateStringFormatter::format($date_str);
        $dir = $this->getDir();

        $prev = null;
        foreach ( $dir as $key => $it ) {
            if ( $key >= $dStr ) {
                if ( null === $prev ) {
                    throw new Exc\ENbpEntryNotFound();
                }
                return $prev;
            }
            if ( isset($it[$type]) ) {
                $prev = $it[$type];
            }
        }

        if ( null !== $prev ) {
            return $prev;
        }

        throw new Exc\ENbpEntryNotFound();
    }

    /**
     * @param string $date_str
     * @param string $type
     * @return string
     */
    public function getFilePath($date_str, $type = 'a')
    {
        $file_name = $this->getFileName($date_str, $type);
        $path = $this->makeFilePath($file_name);
        return $path;
    }

    /**
     * @param string $file_name
     * @return string
     */
    public function makeFilePath($file_name)
    {
        $path = sprintf(self::XML_URL_PATTERN, $file_name);
        return $path;
    }

    /**
     * @param string $date_str
     * @param string $type
     * @return string
     */
    public function getFilePathBefore($date_str, $type = 'a')
    {
        $file_name = $this->getFileNameBefore($date_str, $type);
        $path = $this->makeFilePath($file_name);
        return $path;
    }

    /**
     * @param string $date_str
     * @return NbpRateTuple[]
     */
    public function getRates($date_str)
    {
        $file_name = $this->getFileName($date_str, 'a');
        $rates = $this->_doGetRates($file_name);
        return $rates;
    }

    /**
     * @param string $date_str
     * @return NbpRateTuple[]
     */
    public function getRatesBefore($date_str)
    {
        $file_name = $this->getFileNameBefore($date_str);
        $rates = $this->_doGetRates($file_name);
        return $rates;
    }

    /**
     * @param string $date_str
     * @param string $cur_code
     * @return NbpRateTuple
     * @throws Exc\ENbpEntryNotFound
     */
    public function getRate($date_str, $cur_code)
    {
        $rates = $this->getRates($date_str);
        if ( !isset($rates[$cur_code]) ) {
            throw new Exc\ENbpEntryNotFound();
        }
        return $rates[$cur_code];
    }

    /**
     * @param string $date_str
     * @param string $cur_code
     * @return NbpRateTuple
     * @throws Exc\ENbpEntryNotFound
     */
    public function getRateBefore($date_str, $cur_code)
    {
        $rates = $this->getRatesBefore($date_str);
        if ( !isset($rates[$cur_code]) ) {
            throw new Exc\ENbpEntryNotFound();
        }
        return $rates[$cur_code];
    }

    /**
     * @return array
     */
    public function getDir()
    {
        $url = self::DIR_URL;
        $dir = $this->_NbpCache->tryGet($url);
        if ( empty($dir) ) {
            $DirLoader = new Service\NbpDirLoader($url);
            $dir = $DirLoader->load($url);
            $this->_NbpCache->set($url, $dir);
        }
        return $dir;
    }

    /**
     * @param string $file_name
     * @return NbpRateTuple[]
     */
    protected function _doGetRates($file_name)
    {
        $file_path = $this->makeFilePath($file_name);

        $rates = $this->_NbpCache->tryGet($file_path);
        if ( empty($rates) ) {
            $rates = $this->_getNbpFileLoader()->load($file_path);
            $this->_NbpCache->set($file_path, $rates);
        }
        return $rates;
    }

    /**
     * @param string $dStr
     * @param string $type
     * @return StdClass
     * @throws Exc\ENbpEntryNotFound
     */
    protected function _doGetFileName($dStr, $type)
    {
        $dir = $this->getDir();
        if( !isset($dir[$dStr][$type]) ) {
            throw new Exc\ENbpEntryNotFound();
        }
        return $dir[$dStr][$type];
    }

    /**
     * @return NbpFileLoader
     */
    protected function _getNbpFileLoader()
    {
        if ( null === $this->_NbpFileLoader ) {
            $this->_NbpFileLoader = new NbpFileLoader();
        }
        return $this->_NbpFileLoader;
    }
}
