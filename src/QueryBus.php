<?php

namespace Nihilus\Handling;

use Nihilus\Handling\Exceptions\UnknowQueryException;

class QueryBus implements QueryBusInterface
{
    /**
     * @var QueryHandlerResolverInterface
     */
    private $queryHandlerResolver;

    /**
     * @var PipelineResolverInterface
     */
    private $pipelineResolver;

    public function __construct(QueryHandlerResolverInterface $queryHandlerResovler, QueryPipelineResolverInterface $pipelineResolver)
    {
        $this->queryHandlerResolver = $queryHandlerResovler;
        $this->pipelineResolver = $pipelineResolver;
    }

    public function execute(QueryInterface $query): object
    {
        $handler = $this->queryHandlerResolver->get($query);

        if (null === $handler) {
            throw new UnknowQueryException($query);
        }

        $pipelines = $this->pipelineResolver->getGlobalQueryPipelines();

        $pipelineDispatcher = new QueryPipelineDispatcher($handler);

        foreach ($pipelines as $pipeline) {
            $pipelineDispatcher->addPipeline($pipeline);
        }

        return $pipelineDispatcher->handle($query);
    }
}
