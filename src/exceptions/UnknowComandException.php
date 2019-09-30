<?php

namespace Nihilus\Handling\Exceptions;

use Exception;
use Nihilus\Handling\CommandInterface;

class UnknowCommandException extends Exception
{
    public function __construct(CommandInterface $command)
    {
        $class = get_class($command);
        parent::__construct("Unkow command: {$class}");
    }
}
