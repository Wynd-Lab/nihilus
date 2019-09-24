<?php

namespace Sypontor\Nihilus;

use Sypontor\Nihilus\CommandInterface;

interface HandlerInterface
{
    public function handle(CommandInterface $command);
}
