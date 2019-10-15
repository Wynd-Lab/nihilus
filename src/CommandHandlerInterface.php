<?php

declare(strict_types=1);

namespace Nihilus;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}
