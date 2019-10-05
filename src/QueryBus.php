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
     * @var QueryPipelineResolverInterface
     */
    private $queryPipelineResolver;

    public function __construct(QueryHandlerResolverInterface $queryHandlerResovler, QueryPipelineResolverInterface $pipelineResolver)
    {
        $this->queryHandlerResolver = $queryHandlerResovler;
        $this->queryPipelineResolver = $pipelineResolver;
    }

    public function execute(QueryInterface $query): object
    {
        $handler = $this->queryHandlerResolver->get($query);

        if (null === $handler) {
            throw new UnknowQueryException($query);
        }

        $pipelines = $this->queryPipelineResolver->getGlobals();

        $pipelineDispatcher = new QueryPipelineDispatcher($handler);

        foreach ($pipelines as $pipeline) {
            $pipelineDispatcher->addPipeline($pipeline);
        }

        return $pipelineDispatcher->handle($query);
    }
}
