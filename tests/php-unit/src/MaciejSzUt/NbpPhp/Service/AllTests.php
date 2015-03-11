<?php
namespace MaciejSzUt\NbpPhp\Service;

class AllTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $Suite = new self();

        $Suite->addTestSuite(NbpDateStringFormatterTest::class);

        return $Suite;
    }
}
 