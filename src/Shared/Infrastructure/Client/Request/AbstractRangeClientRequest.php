<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Infrastructure\Client\Request;

use MaciejSz\Nbp\Shared\Infrastructure\Client\Request\NbpClientRequest;

/**
 * @internal
 */
abstract class AbstractRangeClientRequest implements NbpClientRequest
{
    /** @var string */
    protected $startDate;
    /** @var string */
    protected $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}
