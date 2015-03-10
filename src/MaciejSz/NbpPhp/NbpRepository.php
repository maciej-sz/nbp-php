<?php
namespace MaciejSz\NbpPhp;

use stdClass as StdClass;
use Doctrine\Common\Cache\Cache;

class NbpRepository
{
    const DIR_URL = 'http://www.nbp.pl/kursy/xml/dir.txt';

    const XML_URL_PATTERN = 'http://www.nbp.pl/kursy/xml/%s.xml';

    /**
     * @var array
     */
    protected $_dir = [];

    /**
     * @var array
     */
    protected $_loaded_rates = [];

    /**
     * @var Cache
     */
    protected $_Cache = null;

    /**
     * @param Cache $Cache
     */
    public final function __construct(Cache $Cache = null)
    {
        $this->_Cache = $Cache;
    }

    /**
     *
     * @param string $date_str Date in format rrrr-mm-dd
     * @throws Exc\EWrongNbpDateFormat
     * @return string Date in format rrmmdd
     */
    public static function generateDateStr($date_str)
    {
        if ( 10 != strlen($date_str) ) {
            throw new Exc\EWrongNbpDateFormat(
                "Wrong date format: {$date_str}. Should be rrrr-mm-dd."
            );
        }

        $dStr =
            substr($date_str, 2, 2)
            . substr($date_str, 5, 2)
            . substr($date_str, 8, 2);

        return $dStr;
    }

    /**
     * @param string $date_str
     * @param string $type
     * @return string
     */
    public function getFileName($date_str, $type = 'a')
    {
        $dStr = self::generateDateStr($date_str);
        $this->_ensureLoadDir();

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
        $dStr = self::generateDateStr($date_str);
        $this->_ensureLoadDir();

        $prev = null;
        foreach ( $this->_dir as $key => $it ) {
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
    public function getAvgRates($date_str)
    {
        $file_name = $this->getFileName($date_str, 'a');
        $rates = $this->_doGetAvgRates($file_name);
        return $rates;
    }

    /**
     * @param string $date_str
     * @return NbpRateTuple[]
     */
    public function getAvgRatesBefore($date_str)
    {
        $file_name = $this->getFileNameBefore($date_str);
        $rates = $this->_doGetAvgRates($file_name);
        return $rates;
    }

    /**
     * @param string $date_str
     * @param string $cur_code
     * @return NbpRateTuple
     * @throws Exc\ENbpEntryNotFound
     */
    public function getAvgRate($date_str, $cur_code)
    {
        $rates = $this->getAvgRates($date_str);
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
    public function getAvgRateBefore($date_str, $cur_code)
    {
        $rates = $this->getAvgRatesBefore($date_str);
        if ( !isset($rates[$cur_code]) ) {
            throw new Exc\ENbpEntryNotFound();
        }
        return $rates[$cur_code];
    }

    /**
     * @param string $file_name
     * @return NbpRateTuple[]
     */
    protected function _doGetAvgRates($file_name)
    {
        $file_path = $this->makeFilePath($file_name);

        if ( ! isset($this->_loaded_rates[$file_name]) ) {
            $txt = file_get_contents($file_path);
            $Xml = new \SimpleXMLElement($txt);
            /** @var NbpRateTuple[] $rates */
            $rates = [];
            foreach ( $Xml->pozycja as $SxPos ) {
                $Item = NbpRateTuple::fromNbpXml($SxPos, $Xml);
                $rates[$Item->currency_code] = $Item;
            }

            $Item = NbpRateTuple::factory('pln', 'PLN', 1.0, $Xml);
            $rates[$Item->currency_code] = $Item;

            $this->_loaded_rates[$file_name] = $rates;
        }

        return $this->_loaded_rates[$file_name];
    }

    /**
     * @return void
     */
    protected function _ensureLoadDir()
    {
        if ( empty($this->_dir) ) {
            $txt = file_get_contents(self::DIR_URL);
            $dates = explode("\n", $txt);
            foreach ( $dates as $date ) {
                $date = trim($date);
                if ( empty($date) ) {
                    continue;
                }
                $start = strlen($date) - 6;
                $index = substr($date, $start, 6);
                $exttype = substr($date, 0, 1);
                if ( ! isset($this->_dir[$index]) ) {
                    $this->_dir[$index] = array();
                }

                $this->_dir[$index][$exttype] = $date;
            }
            ksort($this->_dir);
        }
    }

    /**
     * @param string $dStr
     * @param string $type
     * @return StdClass
     * @throws Exc\ENbpEntryNotFound
     */
    protected function _doGetFileName($dStr, $type)
    {
        if( !isset($this->_dir[$dStr][$type]) ) {
            throw new Exc\ENbpEntryNotFound();
        }
        return $this->_dir[$dStr][$type];
    }
}
