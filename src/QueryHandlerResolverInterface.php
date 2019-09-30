<?php

namespace Nihilus\Handling;

interface QueryHandlerResolverInterface
{
    public function get(QueryInterface $query): ?QueryHandlerInterface;
}
