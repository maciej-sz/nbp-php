<?php

declare(strict_types=1);

namespace MaciejSz\Nbp\Shared\Domain\Exception;

class TableNotFoundException extends \OutOfBoundsException implements NbpException
{
}
