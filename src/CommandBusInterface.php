<?php

declare(strict_types=1);

namespace Nihilus;

interface CommandBusInteface
{
    public function execute(CommandInterface $command): void;
}
