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
     * @var QueryPipelineResolverInterface
     */
    private $pipelineResolver;

    public function __construct(QueryHandlerResolverInterface $handlerResolver, QueryPipelineResolverInterface $pipelineResolver)
    {
        $this->handlerResolver = $handlerResolver;
        $this->pipelineResolver = $pipelineResolver;
    }

    public function execute(QueryInterface $query): object
    {
        $queryHandler = $this->handlerResolver->get($query);

        if (null === $queryHandler) {
            throw new UnknowQueryException($query);
        }

        $queryPipelines = $this->pipelineResolver->getGlobals();

        $pipelineDispatcher = new QueryPipelineDispatcher($queryHandler);

        foreach ($queryPipelines as $queryPipeline) {
            $pipelineDispatcher->addPipeline($queryPipeline);
        }

        return $pipelineDispatcher->handle($query);
    }
}
