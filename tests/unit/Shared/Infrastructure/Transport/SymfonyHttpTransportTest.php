<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Transport\SymfonyHttpTransport;
use PHPUnit\Framework\TestCase;

class SymfonyHttpTransportTest extends TestCase
{
    public function testCreateInstanceWithDefaultClient()
    {
        self::expectNotToPerformAssertions();
        new SymfonyHttpTransport();
    }
}
