<?php
namespace MaciejSz\NbpPhp;
 
class NbpRateTuple
{
    /**
     * @var null|string
     */
    public $currency_name = null;

    /**
     * @var null|string
     */
    public $currency_code = null;

    /**
     * @var null|float
     */
    public $avg = null;

    /**
     * @var null|string
     */
    public $date = null;

    /**
     * @param \SimpleXMLElement $Sx
     * @param null|\SimpleXMLElement $SxRoot [optional]
     * @return NbpRateTuple
     */
    public static function fromNbpXml(
        \SimpleXMLElement $Sx,
        \SimpleXMLElement $SxRoot = null
    )
    {
        $Instance = new self();
        $Instance->currency_name = (string)$Sx->nazwa_waluty;
        $Instance->currency_code = (string)$Sx->kod_waluty;

        $multiplier = self::_makeFloat($Sx->przelicznik);
        $Instance->avg = $multiplier * self::_makeFloat($Sx->kurs_sredni);


        $Instance->date = self::tryExtractPublishDate($SxRoot);

        return $Instance;
    }

    /**
     * @param string $name
     * @param string $code
     * @param float $avg
     * @param null|\SimpleXMLElement $SxRoot [optional]
     * @return NbpRateTuple
     */
    public static function factory($name, $code, $avg, \SimpleXMLElement $SxRoot = null)
    {
        $Instance = new self();
        $Instance->currency_name = $name;
        $Instance->currency_code = $code;
        $Instance->avg = $avg;
        $Instance->date = self::tryExtractPublishDate($SxRoot);
        return $Instance;
    }

    /**
     * @param null|\SimpleXMLElement $SxRoot [optional]
     * @return null|string
     */
    public static function tryExtractPublishDate(\SimpleXMLElement $SxRoot = null)
    {
        if ( null === $SxRoot ) {
            return null;
        }
        $date = (string)$SxRoot->data_publikacji;
        return $date;
    }

    /**
     * @param mixed $mVal
     * @return float|mixed|string
     */
    private static function _makeFloat($mVal)
    {
        $mVal = (string)$mVal;
        $mVal = str_replace(',', '.', $mVal);
        $mVal = round(floatval($mVal), 4);
        return $mVal;
    }
}
 