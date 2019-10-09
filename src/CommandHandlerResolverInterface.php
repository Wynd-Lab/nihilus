<?php

namespace Nihilus;

interface CommandHandlerResolverInterface
{
    public function get(CommandInterface $command): ?CommandHandlerInterface;
}
