<?php

declare(strict_types=1);

namespace Nihilus;

interface CommandBusInterface
{
    public function execute(CommandInterface $command): void;
    public function publish(CommandInterface $command): void;
}
