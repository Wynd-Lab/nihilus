<?php

declare(strict_types=1);

namespace Nihilus;

interface QueryMiddlewareResolverInterface
{
    /**
     * @return QueryMiddlewareInterface[]
     */
    public function getGlobals(): array;
}
