<?php

namespace Nihilus;

use Exception;

class UnknowCommandException extends Exception
{
    public function __construct(CommandInterface $command)
    {
        $class = get_class($command);
        parent::__construct("Unkow command: {$class}");
    }
}
