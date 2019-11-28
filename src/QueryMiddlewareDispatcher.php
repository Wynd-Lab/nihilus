<?php

declare(strict_types=1);

namespace Nihilus;

/**
 * @internal
 */
class QueryMiddlewareDispatcher
{
    /**
     * @var QueryHandlerInterface
     */
    private $queryHandler;

    public function __construct(QueryHandlerInterface $queryHandler)
    {
        $this->queryHandler = $queryHandler;
    }

    public function addMiddleware(QueryMiddlewareInterface $queryMiddleware): void
    {
        $next = $this->queryHandler;
        $this->queryHandler = new class($queryMiddleware, $next) implements QueryHandlerInterface {
            private $queryMiddleware;
            private $next;

            public function __construct(QueryMiddlewareInterface $queryMiddleware, QueryHandlerInterface $next)
            {
                $this->queryMiddleware = $queryMiddleware;
                $this->next = $next;
            }

            public function handle(QueryInterface $query)
            {
                return $this->queryMiddleware->handle($query, $this->next);
            }
        };
    }

    public function handle(QueryInterface $query)
    {
        return $this->queryHandler->handle($query);
    }
}
