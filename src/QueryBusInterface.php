<?php

namespace Nihilus\Handling;

use Nihilus\Handling\QueryInterface;

interface QueryBusInterface
{
    function execute(QueryInterface $query);
}