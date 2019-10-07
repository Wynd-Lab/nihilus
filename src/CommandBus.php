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
        $commandHandler = $this->commandHandlerResolver->get($command);

        if (null === $commandHandler) {
            throw new UnknowCommandException($command);
        }

        $commandPipelines = $this->commandPipelineResolver->getGlobals();

        $commandPipelineDispatcher = new CommandPipelineDispatcher($commandHandler);

        foreach ($commandPipelines as $commandPipeline) {
            $commandPipelineDispatcher->addPipeline($commandPipeline);
        }

        $commandPipelineDispatcher->handle($command);
    }
}
