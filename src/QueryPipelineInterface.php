<?php

declare(strict_types=1);

namespace Nihilus;

interface QueryPipelineInterface
{
    public function handle(QueryInterface $query, QueryHandlerInterface $next): object;
}
