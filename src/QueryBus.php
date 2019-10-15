<?php

declare(strict_types=1);

namespace Nihilus;

class QueryBus implements QueryBusInterface
{
    /**
     * @var QueryHandlerResolverInterface
     */
    private $queryHandlerResolver;

    /**
     * @var QueryPipelineResolverInterface
     */
    private $queryPipelineResolver;

    public function __construct(QueryHandlerResolverInterface $queryHandlerResolver, QueryPipelineResolverInterface $queryPipelineResolver)
    {
        $this->queryHandlerResolver = $queryHandlerResolver;
        $this->queryPipelineResolver = $queryPipelineResolver;
    }

    public function execute(QueryInterface $query): object
    {
        $queryHandler = $this->queryHandlerResolver->get($query);

        if (null === $queryHandler) {
            throw new UnknowQueryException($query);
        }

        $queryPipelines = $this->queryPipelineResolver->getGlobals();

        $queryPipelineDispatcher = new QueryPipelineDispatcher($queryHandler);

        foreach ($queryPipelines as $queryPipeline) {
            $queryPipelineDispatcher->addPipeline($queryPipeline);
        }

        return $queryPipelineDispatcher->handle($query);
    }
}
