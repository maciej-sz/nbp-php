<?php
namespace MaciejSz\NbpPhp\Stream;
 
class DefaultStreamReader implements IStreamReader
{
    /**
     * @param string $url
     * @return string
     */
    public function getContents($url)
    {
        return file_get_contents($url);
    }

}
 