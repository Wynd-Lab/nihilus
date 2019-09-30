<?php

namespace Nihilus\Tests\Context;

use Nihilus\Handling\QueryHandlerInterface;
use Nihilus\Handling\QueryInterface;

class TestQueryHandler implements QueryHandlerInterface
{
    public function handle(QueryInterface $command): object
    {
        return new TestQueryReadModel($command->getProp());
    }
}
