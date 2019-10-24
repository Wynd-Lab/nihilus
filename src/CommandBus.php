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
     * @var CommandMiddlewareResolverInterface
     */
    private $middlewareResolver;

    public function __construct(CommandHandlerResolverInterface $handlerResolver, CommandMiddlewareResolverInterface $middlewareResolver)
    {
        $this->handlerResolver = $handlerResolver;
        $this->middlewareResolver = $middlewareResolver;
    }

    public function execute(CommandInterface $command): void
    {
        $commandHandler = $this->handlerResolver->get($command);

        if (null === $commandHandler) {
            throw new UnknowCommandException($command);
        }

        $commandMiddlewares = $this->middlewareResolver->get($command);

        $middlewareDispatcher = new CommandMiddlewareDispatcher($commandHandler);

        foreach ($commandMiddlewares as $commandMiddleware) {
            $middlewareDispatcher->addMiddleware($commandMiddleware);
        }

        $middlewareDispatcher->handle($command);
    }
}
