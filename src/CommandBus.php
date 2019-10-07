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
     * @var CommandPipelineResolverInterface
     */
    private $commandPipelineResolver;

    public function __construct(CommandHandlerResolverInterface $commandHandlerResolver, CommandPipelineResolverInterface $commandpipelineResolver)
    {
        $this->commandHandlerResolver = $commandHandlerResolver;
        $this->commandPipelineResolver = $commandpipelineResolver;
    }

    public function execute(CommandInterface $command): void
    {
        $handler = $this->commandHandlerResolver->get($command);

        if (null === $handler) {
            throw new UnknowCommandException($command);
        }

        $pipelines = $this->commandPipelineResolver->getGlobalCommandPipelines();

        $pipelineDispatcher = new PipelineDispatcher($handler);

        foreach ($pipelines as $pipeline) {
            $pipelineDispatcher->addPipeline($pipeline);
        }

        $pipelineDispatcher->handle($command);
    }
}
