<?php

declare(strict_types=1);

namespace Nihilus;

class CommandBus implements CommandBusInterface
{
    /**
     * @var CommandHandlerResolverInterface
     */
    private $handlerResolver;

    /**
     * @var CommandPipelineResolverInterface
     */
    private $pipelineResolver;

    public function __construct(CommandHandlerResolverInterface $handlerResolver, CommandPipelineResolverInterface $pipelineResolver)
    {
        $this->handlerResolver = $handlerResolver;
        $this->pipelineResolver = $pipelineResolver;
    }

    public function execute(CommandInterface $command): void
    {
        $commandHandler = $this->handlerResolver->get($command);

        if (null === $commandHandler) {
            throw new UnknowCommandException($command);
        }

        $commandPipelines = $this->pipelineResolver->getGlobals();

        $pipelineDispatcher = new CommandPipelineDispatcher($commandHandler);

        foreach ($commandPipelines as $commandPipeline) {
            $pipelineDispatcher->addPipeline($commandPipeline);
        }

        $pipelineDispatcher->handle($command);
    }
}
