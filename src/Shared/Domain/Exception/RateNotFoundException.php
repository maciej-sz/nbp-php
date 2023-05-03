<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Domain\Exception;

class RateNotFoundException extends \OutOfRangeException implements NbpException
{
}
