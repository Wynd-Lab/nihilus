<?php

namespace Nihilus\Handling;

use Nihilus\Handling\Exceptions\UnknowCommandException;

class CommandBus implements CommandBusInteface
{
    /**
     * @var CommandHandlerResolverInterface
     */
    private $commandHandlerResolver;

    /**
     * @var PipelineResolverInterface
     */
    private $pipelineResolver;

    public function __construct(CommandHandlerResolverInterface $commandHandlerResolver, PipelineResolverInterface $pipelineResolver)
    {
        $this->commandHandlerResolver = $commandHandlerResolver;
        $this->pipelineResolver = $pipelineResolver;
    }

    public function execute(CommandInterface $command): void
    {
        $handler = $this->commandHandlerResolver->get($command);

        if (null === $handler) {
            throw new UnknowCommandException($command);
        }

        $pipelines = $this->pipelineResolver->getGlobalCommandPipelines();

        $pipelineDispatcher = new PipelineDispatcher($handler);

        foreach ($pipelines as $pipeline) {
            $pipelineDispatcher->addPipeline($pipeline);
        }

        $pipelineDispatcher->handle($command);
    }
}
