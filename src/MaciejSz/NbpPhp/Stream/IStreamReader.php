<?php
namespace MaciejSz\NbpPhp\Stream;
 
interface IStreamReader
{
    /**
     * @param string $url
     * @return string
     */
    public function getContents($url);
}
 