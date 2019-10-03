<?php

namespace Nihilus\Handling;

/**
 * @internal
 */
class PipelineDispatcher
{
    /**
     * @var CommandHandlerInterface
     */
    private $handler;

    public function __construct(CommandHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function addPipeline(PipelineInterface $pipeline): void
    {
        $next = $this->handler;
        $this->handler = new class($pipeline, $next) implements CommandHandlerInterface {
            private $pipeline;
            private $next;

            public function __construct(PipelineInterface $pipeline, CommandHandlerInterface $next)
            {
                $this->pipeline = $pipeline;
                $this->next = $next;
            }

            public function handle(CommandInterface $command): void
            {
                $this->pipeline->handle($command, $this->next);
            }
        };
    }

    public function handle(CommandInterface $command): void
    {
        $this->handler->handle($command);
    }
}
