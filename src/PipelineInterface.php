<?php

namespace Nihilus\Handling;

interface PipelineInterface
{
    public function handle(CommandInterface $command, CommandHandlerInterface $next): void;
}
