<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Test\Unit\Shared\Infrastructure\Serializer;

use MaciejSz\Nbp\Shared\Infrastructure\Serializer\Exception\DataKeyDoesNotExist;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\ArrayDataAccess;
use MaciejSz\Nbp\Shared\Infrastructure\Serializer\Exception\UnexpectedDataType;
use PHPUnit\Framework\TestCase;

class ArrayDataAccessTest extends TestCase
{
    public function testExtract(): void
    {
        $dataAccess = new ArrayDataAccess(['foo' => 'bar']);
        self::assertSame('bar', $dataAccess->extract('foo'));
    }

    public function testExtractMissing(): void
    {
        self::expectException(DataKeyDoesNotExist::class);
        self::expectExceptionMessage('Data key \'bogus\' does not exist');

        $dataAccess = new ArrayDataAccess([]);
        $dataAccess->extract('bogus');
    }

    public function testExtractString(): void
    {
        $dataAccess = new ArrayDataAccess(['foo' => 'bar']);
        self::assertSame('bar', $dataAccess->extractString('foo'));
    }

    public function testExtractStringObject(): void
    {
        $dataAccess = new ArrayDataAccess(['foo' => new class {
            public function __toString()
            {
                return __FUNCTION__;
            }
        }]);
        self::assertSame('__toString', $dataAccess->extractString('foo'));
    }

    public function testExtractStringUnexpectedType(): void
    {
        self::expectException(UnexpectedDataType::class);
        self::expectExceptionMessage('Expected string, got integer');

        $dataAccess = new ArrayDataAccess(['foo' => 123]);
        $dataAccess->extractString('foo');
    }

    public function testExtractFloat(): void
    {
        $dataAccess = new ArrayDataAccess(['foo' => 123.4]);
        self::assertSame(123.4, $dataAccess->extractFloat('foo'));
    }

    public function testExtractFloatFromInt()
    {
        $dataAccess = new ArrayDataAccess(['foo' => 123]);
        self::assertSame(123.0, $dataAccess->extractFloat('foo'));
    }

    public function testExtractFloatUnexpectedType(): void
    {
        self::expectException(UnexpectedDataType::class);
        self::expectExceptionMessage('Expected float, got string');

        $dataAccess = new ArrayDataAccess(['foo' => 'bar']);
        $dataAccess->extractFloat('foo');
    }

    public function testExtractDateTime(): void
    {
        $dataAccess = new ArrayDataAccess(['foo' => '2023-01-01+02:00']);
        self::assertSame(
            '2023-01-01T00:00:00+02:00',
            $dataAccess->extractDateTime('foo')->format('c')
        );
    }

    public function testExtractDateTimeUnexpectedType(): void
    {
        self::expectException(UnexpectedDataType::class);
        self::expectExceptionMessage('Expected valid date, got string');

        $dataAccess = new ArrayDataAccess(['foo' => 'bogus']);
        $dataAccess->extractDateTime('foo');
    }

    public function testExtractArray(): void
    {
        $dataAccess = new ArrayDataAccess(['foo' => [1, 2, 3]]);
        self::assertSame([1, 2, 3], $dataAccess->extractArray('foo'));
    }

    public function testExtractArrayUnexpectedType(): void
    {
        self::expectException(UnexpectedDataType::class);
        self::expectExceptionMessage('Expected array, got string');

        $dataAccess = new ArrayDataAccess(['foo' => 'bogus']);
        $dataAccess->extractArray('foo');
    }
}
