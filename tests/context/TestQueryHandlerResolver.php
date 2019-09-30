<?php

namespace Nihilus\Tests\Context;

use Exception;
use Nihilus\Handling\QueryHandlerInterface;
use Nihilus\Handling\QueryHandlerResolverInterface;
use Nihilus\Handling\QueryInterface;
use ReflectionClass;

class TestQueryHandlerResolver implements QueryHandlerResolverInterface
{
    private $queryHandlers = [];

    public function add(string $queryClass, string $handlerClass)
    {
        $this->queryHandlers[$queryClass] = $handlerClass;
    }

    public function get(QueryInterface $query): ?QueryHandlerInterface
    {
        try {
            $handlerClass = $this->queryHandlers[get_class($query)];
            $reflect = new ReflectionClass($handlerClass);

            return $reflect->newInstance();
        } catch (Exception $e) {
            return null;
        }
    }
}
