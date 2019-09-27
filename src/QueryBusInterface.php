<?php

namespace Nihilus\Handling;

interface QueryBusInterface
{
    public function execute(QueryInterface $query);
}
