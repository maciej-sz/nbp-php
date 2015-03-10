<?php
namespace MaciejSz\NbpPhp\Service;
 
class NbpDirLoader
{
    public function load($url)
    {
        $dir = [];
        $txt = file_get_contents($url);
        $dates = explode("\n", $txt);
        foreach ( $dates as $date ) {
            $date = trim($date);
            if ( empty($date) ) {
                continue;
            }
            $start = strlen($date) - 6;
            $index = substr($date, $start, 6);
            $ext_type = substr($date, 0, 1);
            if ( ! isset($dir[$index]) ) {
                $dir[$index] = array();
            }

            $dir[$index][$ext_type] = $date;
        }
        ksort($dir);
        return $dir;
    }
}
 