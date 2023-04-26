<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Serializer;

use MaciejSz\Nbp\Shared\Domain\DateTimeBuilder;
use MaciejSz\Nbp\Shared\Infrastructure\Exception;

class ArrayDataAccess implements DataAccess
{
    /** @var array */
    private $data;
    /** @var DateTimeBuilder|null */
    private $dateTimeBuilder = null;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function extract(string $key)
    {
        if (!isset($this->data[$key])) {
            throw new Exception\DataKeyDoesNotExist($key);
        }

        return $this->data[$key];
    }

    /**
     * @throws Exception\UnexpectedDataType
     */
    public function extractString(string $key): string
    {
        $value = $this->extract($key);
        if (is_object($value) && method_exists($value, '__toString')) {
            $value = $value->__toString();
        }
        if (!is_string($value)) {
            throw new Exception\UnexpectedDataType('string', gettype($value));
        }

        return $value;
    }

    /**
     * @throws Exception\UnexpectedDataType
     */
    public function extractFloat(string $key): float
    {
        $value = $this->extract($key);
        if (!is_float($value)) {
            throw new Exception\UnexpectedDataType('float', gettype($value));
        }

        return $value;
    }

    /**
     * @throws Exception\UnexpectedDataType
     */
    public function extractDateTime(string $key): \DateTimeInterface
    {
        $value = $this->extract($key);
        try {
            return $this->getDateTimeBuilder()->build($value);
        } catch (\Exception $e) {
            throw new Exception\UnexpectedDataType('date string', gettype($value));
        }
    }

    /**
     * @throws Exception\UnexpectedDataType
     */
    public function extractArray(string $key): array
    {
        $value = $this->extract($key);
        if (!is_array($value)) {
            throw new Exception\UnexpectedDataType('array', gettype($value));
        }

        return $value;
    }

    private function getDateTimeBuilder(): DateTimeBuilder
    {
        if (null === $this->dateTimeBuilder) {
            $this->dateTimeBuilder = new DateTimeBuilder();
        }

        return $this->dateTimeBuilder;
    }
}
