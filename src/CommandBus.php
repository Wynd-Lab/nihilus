<?php

declare(strict_types=1);

namespace Nihilus;

use Error;
use sizeof;

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

        $this->executeHandler($commandHandler, $command);
    }

    public function publish(CommandInterface $command): Result
    {
        $commandHandlers = $this->handlerResolver->getAll($command);

        if (0 === sizeof($commandHandlers)) {
            throw new UnknowCommandException($command);
        }

        $result = new Result();

        foreach ($commandHandlers as $commandHandler) {
            try {
                $this->executeHandler($commandHandler, $command);
            } catch (Error $e) {
                $result->addError($e);
            }
        }

        return $result;
    }

    private function executeHandler(CommandHandlerInterface $handler, CommandInterface $command)
    {
        $commandMiddlewares = $this->middlewareResolver->get($command);

        $middlewareDispatcher = new CommandMiddlewareDispatcher($handler);

        foreach ($commandMiddlewares as $commandMiddleware) {
            $middlewareDispatcher->addMiddleware($commandMiddleware);
        }

        $middlewareDispatcher->handle($command);
    }
}
