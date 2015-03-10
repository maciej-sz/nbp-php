<?php
namespace MaciejSzUt\NbpPhp;

use MaciejSz\NbpPhp\NbpRepository;

class NbpRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerateDateString()
    {
        $this->markTestIncomplete();

        $test_data = [
            '2015-01-01' => '150101',
            '2010-02-03' => '100203',
            '2010-12-23' => '101213',
        ];

        foreach ( $test_data as $fix => $expected ) {
            $this->assertEquals($expected, NbpRepository::generateDateStr($fix));
        }
    }

    public function testBaseUsageOffline()
    {
        $this->markTestIncomplete();
    }

    public function testWorkDayBeforeOffline()
    {
        $this->markTestIncomplete();
    }

    public function testBaseUsageOnline()
    {

    }
}
 