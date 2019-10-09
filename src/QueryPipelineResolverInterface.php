<?php

namespace Nihilus;

interface QueryPipelineResolverInterface
{
    /**
     * @return QueryPipelineInterface[]
     */
    public function getGlobals(): array;
}
