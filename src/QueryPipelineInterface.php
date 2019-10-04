<?php

namespace Nihilus\Handling;

interface QueryPipelineInterface
{
    public function handle(QueryInterface $query, QueryHandlerInterface $next): object;
}
