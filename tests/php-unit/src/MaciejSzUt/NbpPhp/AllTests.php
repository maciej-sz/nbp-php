<?php
namespace MaciejSzUt\NbpPhp;

class AllTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $Suite = new self();

        $Suite->addTestSuite(Service\NbpDateStringFormatterTest::class);
        $Suite->addTestSuite(NbpRepositoryTest::class);
        $Suite->addTestSuite(NbpRepositoryWithCacheTest::class);

        return $Suite;
    }
}
 