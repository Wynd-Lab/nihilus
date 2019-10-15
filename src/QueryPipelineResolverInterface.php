<?php

declare(strict_types=1);

namespace Nihilus;

interface QueryPipelineResolverInterface
{
    /**
     * @return QueryPipelineInterface[]
     */
    public function getGlobals(): array;
}
