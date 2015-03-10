<?php
namespace MaciejSzUt\NbpPhp\Service;
 
use MaciejSz\NbpPhp\Exc\EWrongNbpDateFormat;
use MaciejSz\NbpPhp\Service\NbpDateStringFormatter;

class NbpDateStringFormatterTest extends \PHPUnit_Framework_TestCase
{
    public function testValidGenerateDateString()
    {
        $test_data = [
            '2015-01-01' => '150101',
            '2010-02-03' => '100203',
            '2010-12-23' => '101223',
        ];

        foreach ( $test_data as $fix => $expected ) {
            $actual = NbpDateStringFormatter::format($fix);
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
                NbpDateStringFormatter::format($fix);
            }
            catch ( \Exception $Exc ) {
            }

            $this->assertNotNull($Exc);
            $this->assertInstanceOf($expected, $Exc);
        }

    }
}
 