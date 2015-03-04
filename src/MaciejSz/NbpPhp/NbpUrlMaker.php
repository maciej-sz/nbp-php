<?php
namespace MaciejSz\NbpPhp;
 
class NbpUrlMaker
{
    /**
     * @var Tuples\NbpUrlMakerData
     */
    private $_Data;

    /**
     * @param Tuples\NbpUrlMakerData $Data
     */
    private function __construct(Tuples\NbpUrlMakerData $Data)
    {
        $this->_Data = $Data;
    }

    /**
     * @param null|string $table [optional]
     * @return NbpUrlMaker
     */
    public static function factory($table = null)
    {
        $table = NbpUrl::getTableOrDefault($table);
        $Data = new Tuples\NbpUrlMakerData($table);
        $Data->dir_url = NbpUrl::DEFAULT_DIR_URL;
        $Data->xml_url = NbpUrl::DEFAULT_XML_URL;
        return new self($Data);
    }

    /**
     * @param NbpUrlMaker $NbpUrlMaker
     * @return NbpUrlMaker
     */
    public static function ensure(NbpUrlMaker $NbpUrlMaker = null)
    {
        if ( null === $NbpUrlMaker ) {
            $NbpUrlMaker = self::factory();
        }
        return $NbpUrlMaker;
    }

    /**
     * @param null|string $dir_url
     * @return NbpUrlMaker
     */
    public function withDirUrl($dir_url)
    {
        $NewData = clone $this->_Data;
        $NewData->dir_url = $dir_url;
        return new self($NewData);
    }

    /**
     * @param null|string $xml_url
     * @return NbpUrlMaker
     */
    public function withXmlUrl($xml_url)
    {
        $NewData = clone $this->_Data;
        $NewData->xml_url = $xml_url;
        return new self($NewData);
    }

    /**
     * @return null|string
     */
    public function tryGetDirUrl()
    {
        return $this->_Data->dir_url;
    }

    /**
     * @return null|string
     */
    public function tryGetTable()
    {
        return $this->_Data->table;
    }

    /**
     * @return null|string
     */
    public function tryGetXmlUrl()
    {
        return $this->_Data->xml_url;
    }
}
 