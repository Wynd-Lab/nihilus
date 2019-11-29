<?php

declare(strict_types=1);

namespace Nihilus;

interface QueryBusInterface
{
    public function execute(QueryInterface $query);
}
