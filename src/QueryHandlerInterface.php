<?php

namespace Nihilus\Handling;

interface QueryHandlerInterface
{
    public function handle(QueryInterface $command): object;
}
