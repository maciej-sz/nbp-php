<?php
namespace MaciejSzUt\NbpPhp;
 
use MaciejSz\NbpPhp\NbpDate;

class NbpDateTest extends \PHPUnit_Framework_TestCase
{
    public function testBase()
    {
        $test_data = [
            '2001-02-03' => '010203',
            '1999-01-02' => '990102',
            '2003-02-34' => '030306', // invalid date
        ];

        foreach ( $test_data as $key => $expected ) {
            $Date = NbpDate::fromDateString($key);
            $this->assertEquals($expected, $Date->toString());
        }
    }
}
 