<?php

namespace Nihilus\Tests\Context;

use Nihilus\Handling\HandlerInterface;
use Nihilus\Handling\QueryInterface;

class TestHandler implements HandlerInterface
{
    public function handle(QueryInterface $command): string
    {
        return $command->getProp();
    }
}
