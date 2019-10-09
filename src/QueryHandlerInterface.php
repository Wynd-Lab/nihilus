<?php

namespace Nihilus;

interface QueryHandlerInterface
{
    public function handle(QueryInterface $query): object;
}
