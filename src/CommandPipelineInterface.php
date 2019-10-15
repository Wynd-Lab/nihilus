<?php

declare(strict_types=1);

namespace Nihilus;

interface CommandPipelineInterface
{
    public function handle(CommandInterface $command, CommandHandlerInterface $next): void;
}
