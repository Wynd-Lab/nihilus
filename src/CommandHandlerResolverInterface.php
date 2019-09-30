<?php

namespace Nihilus\Handling;

interface CommandHandlerResolverInterface
{
    public function get(CommandInterface $command): CommandHandlerInterface;
}
