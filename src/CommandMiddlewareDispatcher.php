<?php

declare(strict_types=1);

namespace Nihilus;

/**
 * @internal
 */
class CommandMiddlewareDispatcher
{
    /**
     * @var CommandHandlerInterface
     */
    private $commandHandler;

    public function __construct(CommandHandlerInterface $commandHandler)
    {
        $this->commandHandler = $commandHandler;
    }

    public function addMiddleware(CommandMiddlewareInterface $commandMiddleware): void
    {
        $next = $this->commandHandler;
        $this->commandHandler = new class($commandMiddleware, $next) implements CommandHandlerInterface {
            private $commandMiddleware;
            private $next;

            public function __construct(CommandMiddlewareInterface $commandMiddleware, CommandHandlerInterface $next)
            {
                $this->commandMiddleware = $commandMiddleware;
                $this->next = $next;
            }

            public function handle(CommandInterface $command): void
            {
                $this->commandMiddleware->handle($command, $this->next);
            }
        };
    }

    public function handle(CommandInterface $command): void
    {
        $this->commandHandler->handle($command);
    }
}
