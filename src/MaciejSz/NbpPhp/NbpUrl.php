<?php
namespace MaciejSz\NbpPhp;
 
class NbpUrl
{
    const DEFAULT_DIR_URL = 'http://www.nbp.pl/kursy/xml/dir.txt';

    const DEFAULT_XML_URL = 'http://www.nbp.pl/kursy/xml/';

    const DEFAULT_TABLE = 'a';

    /**
     * @var null|string
     */
    private $_url = null;

    /**
     * @var array
     */
    private static $_file_names_cache = [];

    /**
     * @param string $url
     */
    private function __construct($url)
    {
        $this->_url = $url;
    }

    /**
     * @param null|string $table
     * @return string
     */
    public static function getTableOrDefault($table)
    {
        if ( null === $table ) {
            return self::DEFAULT_TABLE;
        }
        return $table;
    }

    public static function factory(NbpDate $NbpDate, NbpUrlMaker $NbpUrlMaker = null)
    {
        $date_string = $NbpDate->toString();
    }


    public static function mkFileName(NbpDate $NbpDate, NbpUrlMaker $NbpUrlMaker = null)
    {
        $NbpUrlMaker = NbpUrlMaker::ensure($NbpUrlMaker);
        $dir_url = $NbpUrlMaker->tryGetDirUrl();
        if ( !isset(self::$_file_names_cache[$dir_url]) ) {

        }
    }

    public static function mkWorkDayBeforeFileName(NbpDate $NbpDate, $table = null)
    {
        $table = self::getTableOrDefault($table);
    }

    public static function factory(NbpDate $NbpDate, $table = null)
    {
        $table = self::getTableOrDefault($table);
    }

    public static function factoryWorkDayBefore(NbpDate $NbpDate, $table = null)
    {
        $table = self::getTableOrDefault($table);
    }

    private static function _fetchDir($url)
    {
        $items = [];

    }
}
 