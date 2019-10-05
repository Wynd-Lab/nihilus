<?php

namespace Nihilus\Handling;

interface QueryPipelineResolverInterface
{
    /**
     * @return QueryPipelineInterface[]
     */
    public function getGlobals(): array;
}
