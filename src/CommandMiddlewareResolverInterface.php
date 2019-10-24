<?php

declare(strict_types=1);

namespace Nihilus;

interface CommandMiddlewareResolverInterface
{
    /**
     * @return CommandMiddlewareInterface[]
     */
    public function get(CommandInterface $command): array;
}
