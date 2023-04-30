<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Transport\Exception\TransportException;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\FileContentsTransport;
use MaciejSz\Nbp\Test\Fixtures\FixturesRepository;
use PHPUnit\Framework\TestCase;

class FileContentsTransportTest extends TestCase
{
    public function testInvalidJson()
    {
        $fixturesRepository = new FixturesRepository();
        $path = $fixturesRepository->getFullFixturePath('/api/bogus/data', 'json');

        self::expectException(TransportException::class);
        self::expectExceptionMessage('Cannot decode JSON data from ' . $path);

        $transport = new FileContentsTransport('/');
        $transport->fetch($path);
    }
}
