<?php

namespace Nihilus\Handling;

/**
 * @internal
 */
class CommandPipelineDispatcher
{
    /**
     * @var CommandHandlerInterface
     */
    private $commandHandler;

    public function __construct(CommandHandlerInterface $commandHandler)
    {
        $this->commandHandler = $commandHandler;
    }

    public function addPipeline(CommandPipelineInterface $commandPipeline): void
    {
        $next = $this->commandHandler;
        $this->commandHandler = new class($commandPipeline, $next) implements CommandHandlerInterface {
            private $commandPipeline;
            private $next;

            public function __construct(CommandPipelineInterface $commandPipeline, CommandHandlerInterface $next)
            {
                $this->commandPipeline = $commandPipeline;
                $this->next = $next;
            }

            public function handle(CommandInterface $command): void
            {
                $this->commandPipeline->handle($command, $this->next);
            }
        };
    }

    public function handle(CommandInterface $command): void
    {
        $this->commandHandler->handle($command);
    }
}
