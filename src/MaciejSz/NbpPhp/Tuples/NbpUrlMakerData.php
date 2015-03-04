<?php
namespace MaciejSz\NbpPhp\Tuples;
 
class NbpUrlMakerData
{
    /**
     * @var null|string
     */
    public $table = null;

    /**
     * @var null|string
     */
    public $dir_url = null;

    /**
     * @var null|string
     */
    public $xml_url = null;

    /**
     * @param null|string $table [optional]
     */
    public function __construct($table = null)
    {
        $this->table = $table;
    }
}
 