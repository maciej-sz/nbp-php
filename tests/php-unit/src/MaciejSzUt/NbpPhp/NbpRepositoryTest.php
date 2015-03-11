<?php
namespace MaciejSzUt\NbpPhp;

use MaciejSz\NbpPhp\Exc\ENbpEntryNotFound;
use MaciejSz\NbpPhp\Exc\EWrongNbpDateFormat;
use MaciejSz\NbpPhp\NbpRepository;
use MaciejSz\NbpPhp\Service\NbpCache;

class NbpRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var null|NbpCache
     */
    protected static $_NbpCache = null;

    public function testValidGetFileName()
    {
        $Repo = new NbpRepository(self::$_NbpCache);

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
        $Repo = new NbpRepository(self::$_NbpCache);

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
        $Repo = new NbpRepository(self::$_NbpCache);

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
        $Repo = new NbpRepository(self::$_NbpCache);

        $test_data = [
            [['2002-01-02', 'a'], ENbpEntryNotFound::class], // first date in directory
            [['2002-01-01', 'a'], ENbpEntryNotFound::class], // before first date in directory
        ];

        foreach ( $test_data as $data ) {
            $Exc = null;
            try {
                $Repo->getFileNameBefore($data[0][0], $data[0][1]);
            }
            catch ( \Exception $Exc ) {
            }

            $this->assertNotNull($Exc);
            $this->assertInstanceOf($data[1], $Exc);
        }
    }

    public function testValidGetFilePath()
    {
        $Repo = new NbpRepository(self::$_NbpCache);

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
            $expected = "http://www.nbp.pl/kursy/xml/${data[1]}.xml";
            $actual = $Repo->getFilePath($date, $table);
            $this->assertEquals(
                $expected,
                $actual,
                "Failed with (${date}, ${table})"
            );
        }
    }

    public function testGetFilePathBefore()
    {
        $Repo = new NbpRepository(self::$_NbpCache);

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
            $expected = "http://www.nbp.pl/kursy/xml/${data[1]}.xml";
            $actual = $Repo->getFilePathBefore($date, $table);
            $this->assertEquals(
                $expected,
                $actual,
                "Failed with (${date}, ${table})"
            );
        }
    }

    public function testValidGetAvgRate()
    {
        $Repo = new NbpRepository(self::$_NbpCache);

        $test_data = [
            [['2015-01-02', 'USD'], 3.5725],
            [['2015-01-02', 'EUR'], 4.3078],
            [['2015-01-02', 'CHF'], 3.5833],
            [['2011-11-10', 'JPY'], 414.23]
        ];

        foreach ( $test_data as $data ) {
            $date = $data[0][0];
            $currency = $data[0][1];

            $Tuple = $Repo->getAvgRate($date, $currency);

            $this->assertEquals(
                $data[1],
                $Tuple->avg,
                "Failed with (${date}, ${currency})"
            );

            $this->assertEquals($date, $Tuple->date);
            $this->assertEquals($currency, $Tuple->currency_code);
        }
    }

    public function testFailGetAvgRate()
    {
        $Repo = new NbpRepository(self::$_NbpCache);

        $test_data = [
            [['2099-01-02', 'USD'], ENbpEntryNotFound::class],
        ];

        foreach ( $test_data as $data ) {
            $date = $data[0][0];
            $currency = $data[0][1];

            $Exc = null;

            try {
                $Repo->getAvgRate($date, $currency);
            }
            catch ( \Exception $Exc ) {
            }

            $this->assertNotNull($Exc);
            $this->assertInstanceOf($data[1], $Exc);
        }
    }

    public function testValidGetAvgRateBefore()
    {
        $Repo = new NbpRepository(self::$_NbpCache);

        $test_data = [
            [['2013-01-15', 'USD'], [3.0828, '2013-01-14']],
            [['2013-01-15', 'EUR'], [4.1231, '2013-01-14']],
            [['2013-01-15', 'CHF'], [3.3674, '2013-01-14']],
            [['2013-01-14', 'USD'], [3.0890, '2013-01-11']],
            [['2013-01-14', 'EUR'], [4.0996, '2013-01-11']],
            [['2013-01-14', 'CHF'], [3.3693, '2013-01-11']],
        ];

        foreach ( $test_data as $data ) {
            $date = $data[0][0];
            $currency_code = $data[0][1];

            $expected_avg = $data[1][0];
            $expected_date = $data[1][1];

            $Tuple = $Repo->getAvgRateBefore($date, $currency_code);

            $this->assertEquals(
                $expected_avg,
                $Tuple->avg,
                "Failed with (${date}, ${currency_code})",
                0.00001
            );

            $this->assertEquals($expected_date, $Tuple->date);
            $this->assertEquals($currency_code, $Tuple->currency_code);
        }
    }

    public function testFailGetAvgRateBefore()
    {
        $Repo = new NbpRepository(self::$_NbpCache);

        $test_data = [
            [['2099-01-02', 'USD'], ENbpEntryNotFound::class],
            [['150102', 'USD'], EWrongNbpDateFormat::class],
            [['2001-01-01', 'USD'], ENbpEntryNotFound::class],
        ];

        foreach ( $test_data as $data ) {
            $date = $data[0][0];
            $currency = $data[0][1];

            $Exc = null;

            try {
                $Repo->getAvgRateBefore($date, $currency);
            }
            catch ( \Exception $Exc ) {
            }

            $this->assertNotNull($Exc, "While testing (${date}, ${currency})");
            $this->assertInstanceOf($data[1], $Exc);
        }
    }
}
 