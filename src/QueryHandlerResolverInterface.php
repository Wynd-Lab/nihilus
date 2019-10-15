<?php

declare(strict_types=1);

namespace Nihilus;

interface QueryHandlerResolverInterface
{
    public function get(QueryInterface $query): ?QueryHandlerInterface;
}
