<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Transport;

use MaciejSz\Nbp\Shared\Infrastructure\Transport\Exception\TransportException;
use MaciejSz\Nbp\Shared\Infrastructure\Transport\FileContentsTransport;
use MaciejSz\Nbp\Test\Fixtures\FixturesRepository;
use PHPUnit\Framework\TestCase;

class FileContentsTransportTest extends TestCase
{
    public function testDefaultInstance(): void
    {
        $transport = FileContentsTransport::new();
        self::assertInstanceOf(FileContentsTransport::class, $transport);
    }

    public function testGet(): void
    {
        $fixturesRepository = new FixturesRepository();
        $path = $fixturesRepository->getFullFixturePath('/api/sample/array-foo-bar-baz', 'json');

        $transport = new FileContentsTransport('/');
        $data = $transport->get($path);

        self::assertSame(['foo', 'bar', 'baz'], $data);
    }

    public function testGetInvalidJson(): void
    {
        $fixturesRepository = new FixturesRepository();
        $path = $fixturesRepository->getFullFixturePath('/api/bogus/data', 'json');

        self::expectException(TransportException::class);
        self::expectExceptionMessage("Cannot decode JSON data from {$path}");

        $transport = new FileContentsTransport('/');
        $transport->get($path);
    }

    public function testGetInvalidPath(): void
    {
        self::expectException(TransportException::class);
        self::expectExceptionMessage('Cannot get contents from /bogus');

        $transport = new FileContentsTransport('/');

        set_error_handler(
            function (int $errno, string $errstr, string $errfile, int $errline): bool {
                return true;
            }
        );

        $transport->get('bogus');

        restore_error_handler();
    }
}
