<?php

namespace Nihilus\Handling;

interface HandlerInterface
{
    public function handle(QueryInterface $command);
}
