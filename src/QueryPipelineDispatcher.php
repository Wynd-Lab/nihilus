<?php

namespace Nihilus\Handling;

/**
 * @internal
 */
class QueryPipelineDispatcher
{
    /**
     * @var QueryHandlerInterface
     */
    private $queryHandler;

    public function __construct(QueryHandlerInterface $queryHandler)
    {
        $this->queryHandler = $queryHandler;
    }

    public function addPipeline(QueryPipelineInterface $queryPipeline): void
    {
        $next = $this->queryHandler;
        $this->queryHandler = new class($queryPipeline, $next) implements QueryHandlerInterface {
            private $queryPipeline;
            private $next;

            public function __construct(QueryPipelineInterface $queryPipeline, QueryHandlerInterface $next)
            {
                $this->queryPipeline = $queryPipeline;
                $this->next = $next;
            }

            public function handle(QueryInterface $query): object
            {
                return $this->queryPipeline->handle($query, $this->next);
            }
        };
    }

    public function handle(QueryInterface $query): object
    {
        return $this->queryHandler->handle($query);
    }
}
