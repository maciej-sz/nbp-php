<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Transport\GuzzleTransport;
use PHPUnit\Framework\TestCase;

class GuzzleTransportTest extends TestCase
{
    public function testInstantiateWithDefaultClient()
    {
        self::expectNotToPerformAssertions();
        new GuzzleTransport();
    }
}
