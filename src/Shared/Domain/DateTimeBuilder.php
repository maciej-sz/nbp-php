<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Domain;

use MaciejSz\Nbp\Shared\Infrastructure\Validator\Validator;

class DateTimeBuilder
{
    public const DEFAULT_TIMEZONE = 'Europe/Warsaw';

    /** @var string */
    private $timezone;
    /** @var ?Validator<string> */
    private $validator;

    /**
     * @param ?Validator<string> $validator
     */
    public function __construct(
        string $timezone = self::DEFAULT_TIMEZONE,
        ?Validator $validator = null,
    ) {
        $this->timezone = $timezone;
        $this->validator = $validator;
    }

    public function build(string $date): \DateTimeInterface
    {
        if ($this->validator) {
            $this->validator->validate($date);
        }

        return new \DateTimeImmutable($date, new \DateTimeZone($this->timezone));
    }
}
