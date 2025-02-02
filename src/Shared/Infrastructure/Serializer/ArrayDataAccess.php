<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Serializer;

use MaciejSz\Nbp\Shared\Domain\DateTimeBuilder;

class ArrayDataAccess implements DataAccess
{
    /** @var array<array-key, mixed> */
    private $data;
    /** @var ?DateTimeBuilder */
    private $dateTimeBuilder;

    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
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
        if (is_float($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }

        throw new Exception\UnexpectedDataType('float', gettype($value));
    }

    /**
     * @throws Exception\UnexpectedDataType
     */
    public function extractDateTime(string $key): \DateTimeInterface
    {
        $value = $this->extract($key);
        if (!is_string($value)) {
            throw new Exception\UnexpectedDataType('valid date', gettype($value));
        }
        try {
            return $this->getDateTimeBuilder()->build($value);
        } catch (\Exception $e) {
            throw new Exception\UnexpectedDataType('valid date', mb_substr($value, 0, 10));
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
