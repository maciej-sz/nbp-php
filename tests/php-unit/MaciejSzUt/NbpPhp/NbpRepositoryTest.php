<?php
namespace MaciejSzUt\NbpPhp;

use MaciejSz\NbpPhp\Exc\ENbpEntryNotFound;
use MaciejSz\NbpPhp\Exc\EWrongNbpDateFormat;
use MaciejSz\NbpPhp\NbpRepository;

class NbpRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testValidGenerateDateString()
    {
        $test_data = [
            '2015-01-01' => '150101',
            '2010-02-03' => '100203',
            '2010-12-23' => '101223',
        ];

        foreach ( $test_data as $fix => $expected ) {
            $actual = NbpRepository::generateDateStr($fix);
            $this->assertEquals(
                $expected,
                $actual,
                "{$expected} != {$actual}"
            );
        }
    }

    public function testFailGenerateDateString()
    {
        $test_data = [
            '2015-01-012' => EWrongNbpDateFormat::class,
            '2010-02-0' => EWrongNbpDateFormat::class,
        ];

        foreach ( $test_data as $fix => $expected ) {
            $Exc = null;
            try {
                NbpRepository::generateDateStr($fix);
            }
            catch ( \Exception $Exc ) {
            }

            $this->assertNotNull($Exc);
            $this->assertInstanceOf($expected, $Exc);
        }

    }

    public function testValidGetFileName()
    {
        $Repo = new NbpRepository();

        $test_data = [
            [['2015-01-02', 'c'], 'c001z150102'],
            [['2015-01-02', 'h'], 'h001z150102'],
            [['2015-01-02', 'a'], 'a001z150102'],

            [['2010-02-03', 'c'], 'c023z100203'],
            [['2010-02-03', 'h'], 'h023z100203'],
            [['2010-02-03', 'a'], 'a023z100203'],
            [['2010-02-03', 'b'], 'b005z100203'],

            [['2010-12-13', 'c'], 'c241z101213'],
            [['2010-12-13', 'h'], 'h241z101213'],
            [['2010-12-13', 'a'], 'a241z101213'],
        ];

        foreach ( $test_data as $data ) {
            $date = $data[0][0];
            $table = $data[0][1];
            $actual = $Repo->getFileName($date, $table);
            $this->assertEquals(
                $data[1],
                $actual,
                "Failed with (${date}, ${table})"
            );
        }
    }

    public function testFailGetFileName()
    {
        $Repo = new NbpRepository();

        $test_data = [
            [['150102', 'a'], EWrongNbpDateFormat::class],
            [['2015-01-01', 'c'], ENbpEntryNotFound::class],
            [['2015-01-01', 'h'], ENbpEntryNotFound::class],
            [['2015-01-01', 'a'], ENbpEntryNotFound::class],
            [['2015-01-01', 'b'], ENbpEntryNotFound::class],
            [['2010-12-13', 'b'], ENbpEntryNotFound::class],
        ];

        foreach ( $test_data as $data ) {
            $Exc = null;
            try {
                $Repo->getFileName($data[0][0], $data[0][1]);
            }
            catch ( \Exception $Exc ) {
            }

            $this->assertNotNull($Exc);
            $this->assertInstanceOf($data[1], $Exc);
        }
    }

    public function testValidGetFileNameBefore()
    {
        $Repo = new NbpRepository();

        $test_data = [
            [['2010-02-03', 'c'], 'c022z100202'],
            [['2010-02-03', 'h'], 'h022z100202'],
            [['2010-02-03', 'a'], 'a022z100202'],
            [['2010-02-03', 'b'], 'b004z100127'],

            [['2015-01-02', 'c'], 'c252z141231'],
            [['2015-01-02', 'h'], 'h252z141231'],
            [['2015-01-02', 'a'], 'a252z141231'],
            [['2015-01-02', 'b'], 'b052z141231'],

            [['2010-12-13', 'c'], 'c240z101210'],
            [['2010-12-13', 'h'], 'h240z101210'],
            [['2010-12-13', 'a'], 'a240z101210'],
            [['2010-12-13', 'b'], 'b049z101208'],
        ];

        foreach ( $test_data as $data ) {
            $date = $data[0][0];
            $table = $data[0][1];
            $actual = $Repo->getFileNameBefore($date, $table);
            $this->assertEquals(
                $data[1],
                $actual,
                "Failed with (${date}, ${table})"
            );
        }
    }

    public function testFailGetFileNameBefore()
    {
        $Repo = new NbpRepository();

        $test_data = [
            [['2002-01-02', 'a'], ENbpEntryNotFound::class], // first date in directory
            [['2002-01-01', 'a'], ENbpEntryNotFound::class], // before first date in directory
        ];

        foreach ( $test_data as $data ) {
            $Exc = null;
            try {
                $name = $Repo->getFileNameBefore($data[0][0], $data[0][1]);
            }
            catch ( \Exception $Exc ) {
            }

            $this->assertNotNull($Exc);
            $this->assertInstanceOf($data[1], $Exc);
        }
    }

    public function testBaseUsageOffline()
    {
        $this->markTestIncomplete();
    }

    public function testBeforeOffline()
    {
        $this->markTestIncomplete();
    }

    public function testBaseUsageOnline()
    {

        $this->markTestIncomplete();
    }
}
 