<?php

namespace Nihilus;

interface QueryHandlerResolverInterface
{
    public function get(QueryInterface $query): ?QueryHandlerInterface;
}
