<?php

namespace Nihilus\Handling;

class CommandBus implements CommandBusInteface
{
    /**
     * @var CommandHandlerResolverInterface
     */
    private $commandHandlerResolver;

    public function __construct(CommandHandlerResolverInterface $commandHandlerResolver)
    {
        $this->commandHandlerResolver = $commandHandlerResolver;
    }

    public function execute(CommandInterface $command): void
    {
        $handler = $this->commandHandlerResolver->get($command);
        $handler->handle($command);
    }
}
