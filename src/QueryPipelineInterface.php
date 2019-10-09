<?php

namespace Nihilus;

interface QueryPipelineInterface
{
    public function handle(QueryInterface $query, QueryHandlerInterface $next): object;
}
