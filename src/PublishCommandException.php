<?php

declare(strict_types=1);

namespace Nihilus;

use Exception;

class PublishCommandException extends Exception
{
    /**
     * @var Exception[]
     */
    private $exceptions;

    /**
     * @param Exception[] $exceptions
     */
    public function __construct(array $exceptions)
    {
        $this->exceptions = $exceptions;
    }

    public function getHandlerExceptions()
    {
        return $this->exceptions;
    }
}
