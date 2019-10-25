<?php

declare(strict_types=1);

namespace Nihilus\Exceptions;

use Exception;

class PublishException extends Exception
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
