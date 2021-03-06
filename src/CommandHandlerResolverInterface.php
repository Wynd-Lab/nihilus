<?php

declare(strict_types=1);

namespace Nihilus;

interface CommandHandlerResolverInterface
{
    public function get(CommandInterface $command): ?CommandHandlerInterface;

    public function getAll(CommandInterface $command): array;
}
