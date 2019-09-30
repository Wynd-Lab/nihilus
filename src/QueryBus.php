<?php

namespace Nihilus\Handling;

use Nihilus\Handling\Exceptions\UnknowQueryException;

class QueryBus implements QueryBusInterface
{
    /**
     * @var QueryHandlerResolverInterface
     */
    private $queryHandlerResolver;

    public function __construct(QueryHandlerResolverInterface $queryHandlerResovler)
    {
        $this->queryHandlerResolver = $queryHandlerResovler;
    }

    public function execute(QueryInterface $query): object
    {
        $handler = $this->queryHandlerResolver->get($query);

        if (null === $handler) {
            throw new UnknowQueryException($query);
        }

        return $handler->handle($query);
    }
}
