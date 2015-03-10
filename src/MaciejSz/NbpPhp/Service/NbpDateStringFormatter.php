<?php
namespace MaciejSz\NbpPhp\Service;
 
use MaciejSz\NbpPhp\Exc\EWrongNbpDateFormat;

class NbpDateStringFormatter
{
    /**
     *
     * @param string $date_str Date in format rrrr-mm-dd
     * @throws EWrongNbpDateFormat
     * @return string Date in format rrmmdd
     */
    public static function format($date_str)
    {
        if ( 10 != strlen($date_str) ) {
            throw new EWrongNbpDateFormat(
                "Wrong date format: {$date_str}. Should be rrrr-mm-dd."
            );
        }

        $dStr =
            substr($date_str, 2, 2)
            . substr($date_str, 5, 2)
            . substr($date_str, 8, 2);

        return $dStr;
    }
}
 