<?php
namespace MaciejSz\NbpPhp;
 
class NbpRepository
{


    /**
     * @var array
     */
    private static $_dirs_cache = [];

    /**
     * @var array
     */
    private static $_rates_cache = [];

    /**
     * @param null|string $dir_url [optional]
     * @param null|string $xml_url [optional]
     */
    function __construct($dir_url = null, $xml_url = null)
    {
        if ( empty($dir_url) ) {
            $dir_url = self::DIR_URL;
        }
        if ( empty($xml_url) ) {
            $xml_url = self::XML_URL;
        }
        $this->_dir_url = $dir_url;
        $this->_xml_url = $xml_url;
    }

    public function getCurrenciesTableFileUrl()
    {

    }
}
 