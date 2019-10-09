<?php

namespace Nihilus;

interface QueryBusInterface
{
    public function execute(QueryInterface $query): object;
}
