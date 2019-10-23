<?php

declare(strict_types=1);

namespace Nihilus;

interface QueryMiddlewareInterface
{
    public function handle(QueryInterface $query, QueryHandlerInterface $next): object;
}
