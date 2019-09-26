<?php

namespace Nihilus\Handling;

use Nihilus\Handling\QueryInterface;

interface HandlerInterface
{
    function handle(QueryInterface $command);
}
