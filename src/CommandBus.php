<?php

namespace Nihilus\Handling;

use Nihilus\Handling\Exceptions\UnknowCommandException;

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

        if (null === $handler) {
            throw new UnknowCommandException($command);
        }

        $handler->handle($command);
    }
}
