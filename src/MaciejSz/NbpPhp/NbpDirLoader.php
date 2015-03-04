<?php
namespace MaciejSz\NbpPhp;
 
class NbpDirLoader
{
    /**
     * @var Stream\IStreamReader
     */
    private $_StreamReader = null;

    /**
     * @param Stream\IStreamReader $StreamReader
     */
    private function __construct(Stream\IStreamReader $StreamReader)
    {
        $this->_StreamReader = $StreamReader;
    }

    /**
     * @param Stream\IStreamReader $StreamReader
     * @return NbpDirLoader
     */
    public static function factory(Stream\IStreamReader $StreamReader = null)
    {
        if ( null === $StreamReader ) {
            $StreamReader = new Stream\DefaultStreamReader();
        }
        return new self($StreamReader);
    }

    public function load($url)
    {
        $items = [];
        $contents = $this->_StreamReader->getContents($url);

        $line = strtok($contents, "\r\n");
        $line = ltrim($line, "\xEF\xBB\xBF");
        while ( false !== $line ) {
            $line = trim($line);
            if ( empty($line) ) {
                continue;
            }
            $start = mb_strlen($line) - 6;
            $date_part = mb_substr($line, $start);
            $type = substr($line, 0, 1);
            if ( !isset($items[$date_part]) ) {
                $items[$date_part] = [];
            }
            $items[$date_part][$type] = $line;
            $line = strtok("\n");
        }

        return $items;
    }
}
 