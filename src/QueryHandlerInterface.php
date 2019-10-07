<?php

namespace Nihilus\Handling;

interface QueryHandlerInterface
{
    public function handle(QueryInterface $query): object;
}
