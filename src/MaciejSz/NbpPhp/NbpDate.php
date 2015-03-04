<?php
namespace MaciejSz\NbpPhp;
 
class NbpDate
{
    /**
     * @var \DateTime
     */
    private $_Date = null;

    /**
     * @param \DateTime $Date
     */
    protected function __construct(\DateTime $Date)
    {
        $this->_Date = $Date;
    }

    /**
     * @param string $rrrr_mm_dd
     * @return NbpDate
     */
    public static function fromDateString($rrrr_mm_dd)
    {
        $Date = \DateTime::createFromFormat("Y-m-d", $rrrr_mm_dd);
        $NbpDate = new self($Date);
        return $NbpDate;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $str = $this->_Date->format("ymd");
        return $str;
    }
}
 