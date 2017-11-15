<?php
namespace MaciejSzUt\NbpPhp\Service;
 
use MaciejSz\NbpPhp\Service\NbpDirLoader;
use PHPUnit\Framework\TestCase;

class NbpDirLoaderTest extends TestCase
{
    public function testGetUrl()
    {
        $Loader = new NbpDirLoader();
        $data_set = [
            "2015" => "http://www.nbp.pl/kursy/xml/dir2015.txt",
            ""     => "http://www.nbp.pl/kursy/xml/dir.txt",
            "2014" => "http://www.nbp.pl/kursy/xml/dir2014.txt",
            "2013" => "http://www.nbp.pl/kursy/xml/dir2013.txt",
        ];

        foreach ( $data_set as $key => $expected ) {
            $this->assertEquals($expected, $Loader->getUrl($key));
        }
    }

    public function testLoad()
    {

        $this->markTestIncomplete();
    }
}
 