<?php

namespace Nihilus\Handling;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}
