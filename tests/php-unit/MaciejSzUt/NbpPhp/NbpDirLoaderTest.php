<?php
namespace MaciejSzUt\NbpPhp;
 
use MaciejSz\NbpPhp\NbpDirLoader;

class NbpDirLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testBase()
    {
        $Loader = NbpDirLoader::factory();

        $url = __DIR__ . "/fixtures/dir-small.txt";
        $url = ltrim($url, "/");
        $url = "file:///${url}";

        $items = $Loader->load($url);

        $expected = [
            '020102' => [
                'c' => 'c001z020102',
                'h' => 'h001z020102',
                'a' => 'a001z020102',
                'b' => 'b001z020102',
            ],
            '020103' => [
                'c' => 'c002z020103',
                'h' => 'h002z020103',
                'a' => 'a002z020103',
            ],
        ];

        $this->assertEquals(
            print_r($expected, true),
            print_r($items, true)
        );
    }
}
 