<?php

namespace Nihilus\Handling;

interface CommandPipelineInterface
{
    public function handle(CommandInterface $command, CommandHandlerInterface $next): void;
}
