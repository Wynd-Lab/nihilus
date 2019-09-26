<?php

namespace Nihilus\Handling;

use Nihilus\Handling\QueryBusInterface;

interface QueryBusInterface
{
    function execute(QueryInterface $command);
}