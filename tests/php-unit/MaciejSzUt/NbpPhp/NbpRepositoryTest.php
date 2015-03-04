<?php
namespace MaciejSzUt\NbpPhp;
 
use MaciejSz\NbpPhp\NbpDate;
use MaciejSz\NbpPhp\NbpRepository;
use MaciejSz\NbpPhp\NbpUrl;

class NbpRepositoryTest extends \PHPUnit_Framework_TestCase
{
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
        $this->markTestIncomplete();

        $Repo = new NbpRepository();
        $NbpDate = NbpDate::fromDateString('2015-02-22');
        $Url = NbpUrl::factory($NbpDate);
        $Repo->getAvgRate($Url);


        $this->markTestIncomplete();
    }

    public function testWorkDayBeforeOnline()
    {
        $this->markTestIncomplete();

        $Repo = new NbpRepository();
        $NbpDate = NbpDate::fromDateString('2015-02-22');
        $Url = NbpUrl::factoryWorkDayBefore($NbpDate);
        $Repo->getAvgRate($Url);


        $this->markTestIncomplete();
    }
}
 