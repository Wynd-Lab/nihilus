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
    private $handler;

    public function __construct(QueryHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function addPipeline(QueryPipelineInterface $pipeline): void
    {
        $next = $this->handler;
        $this->handler = new class($pipeline, $next) implements QueryHandlerInterface {
            private $pipeline;
            private $next;

            public function __construct(QueryPipelineInterface $pipeline, QueryHandlerInterface $next)
            {
                $this->pipeline = $pipeline;
                $this->next = $next;
            }

            public function handle(QueryInterface $query): object
            {
                return $this->pipeline->handle($query, $this->next);
            }
        };
    }

    public function handle(QueryInterface $query): object
    {
        return $this->handler->handle($query);
    }
}
