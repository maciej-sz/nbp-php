<?php
namespace MaciejSzUt\NbpPhp;
 
class AllTests extends \PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $Suite = new self();

        $Suite->addTestSuite(NbpRepositoryTest::class);

        return $Suite;
    }
}
 