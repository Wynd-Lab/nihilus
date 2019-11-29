<?php

declare(strict_types=1);

namespace Nihilus;

class QueryBus implements QueryBusInterface
{
    /**
     * @var QueryHandlerResolverInterface
     */
    private $handlerResolver;

    /**
     * @var QueryMiddlewareResolverInterface
     */
    private $middlewareResolver;

    public function __construct(QueryHandlerResolverInterface $handlerResolver, QueryMiddlewareResolverInterface $middlewareResolver)
    {
        $this->handlerResolver = $handlerResolver;
        $this->middlewareResolver = $middlewareResolver;
    }

    public function execute(QueryInterface $query)
    {
        $queryHandler = $this->handlerResolver->get($query);

        if (null === $queryHandler) {
            throw new UnknowQueryException($query);
        }

        $queryMiddlewares = $this->middlewareResolver->get($query);

        $middlewareDispatcher = new QueryMiddlewareDispatcher($queryHandler);

        foreach ($queryMiddlewares as $queryMiddleware) {
            $middlewareDispatcher->addMiddleware($queryMiddleware);
        }

        return $middlewareDispatcher->handle($query);
    }
}
