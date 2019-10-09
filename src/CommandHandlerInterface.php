<?php

namespace Nihilus;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}
