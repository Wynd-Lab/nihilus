<?php

namespace Nihilus\Tests;

use Nihilus\CommandHandlerInterface;
use Nihilus\CommandHandlerResolverInterface;
use Nihilus\CommandInterface;
use Nihilus\CommandMiddlewareInterface;
use Nihilus\CommandMiddlewareResolverInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CommandBusTestContext
{
    /**
     * @var TestCase
     */
    private $test;

    /**
     * @var CommandInterface
     */
    private $command;

    /**
     * @var CommandHandlerInterface
     */
    private $commandHandler;

    /**
     * @var CommandHandlerInterface[]
     */
    private $publishHandlers;

    /**
     * @var CommandHandlerResolverInterface
     */
    private $commandHandlerResolver;

    /**
     * @var CommandMiddlewareResolverInterface
     */
    private $commandMiddlewareResolver;

    /**
     * @var CommandMiddlewareInterface[]
     */
    private $middlewares;

    /**
     * @var CommandMiddlewareInterface
     */
    private $commandMiddleware;

    public function __construct(TestCase $test)
    {
        $this->test = $test;
    }

    /**
     * Get the value of command
     *
     * @return  CommandInterface
     */ 
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Get the value of commandHandler
     *
     * @return  CommandHandlerInterface
     */ 
    public function getCommandHandler()
    {
        return $this->commandHandler;
    }

    /**
     * Get the value of commandMiddleware
     *
     * @return  CommandMiddlewareInterface
     */ 
    public function getCommandMiddleware()
    {
        return $this->commandMiddleware;
    }

    /**
     * Get the value of commandHandlerResolver
     *
     * @return  CommandHandlerResolverInterface
     */ 
    public function getCommandHandlerResolver()
    {
        return $this->commandHandlerResolver;
    }

    /**
     * Get the value of commandMiddlewareResolver
     *
     * @return  CommandMiddlewareResolverInterface
     */ 
    public function getCommandMiddlewareResolver()
    {
        return $this->commandMiddlewareResolver;
    }

    public function setUpCommand()
    {
        $this->command = new class() implements CommandInterface {
        };
    }

    public function setUpHandler()
    {
        $this->commandHandler = $this->test
            ->getMockBuilder(CommandHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ; 

        $this->commandHandlerResolver = $this->test
            ->getMockBuilder(CommandHandlerResolverInterface::class)
            ->setMethods((['get', 'getAll']))
            ->getMock()
        ;

        $this->commandHandlerResolver
            ->method('get')
            ->will($this->test
                ->returnCallback(
                    function ($arg) {
                        if($arg === $this->command) {
                            return $this->commandHandler;
                        } else {
                            return null;
                        }
                    }
                )
            )
        ;

        $this->commandHandlerResolver
            ->method('getAll')
            ->will($this->test
                ->returnCallback(
                    function ($arg) {
                        if($arg === $this->command) {
                            return $this->publishHandlers;
                        } else {
                            return [];
                        }
                    }
                )
            )
        ;

        $this->publishHandlers = [$this->commandHandler];
    }

    public function setUpMiddlewares()
    {
        $this->commandMiddleware = $this->test
            ->getMockBuilder(CommandMiddlewareInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->commandMiddleware
            ->method(('handle'))
            ->will($this->test
                ->returnCallback(
                    function($command, $next) {
                        $next->handle($command);
                    }
                )
            )
        ;

        $this->commandMiddlewareResolver = $this->test
            ->getMockBuilder(CommandMiddlewareResolverInterface::class)
            ->setMethods((['get']))
            ->getMock()
        ;

        $this->commandMiddlewareResolver
            ->method('get')
            ->will($this->test
                ->returnCallback(
                    function ($arg) {
                        if($arg === $this->command) {
                            return $this->middlewares;
                        } else {
                            [];
                        }
                        
                    }
                )
            )
        ;

        $this->middlewares = [$this->commandMiddleware];
    }

    public function addMiddleware(CommandMiddlewareInterface $middleware) 
    {
        array_push($this->middlewares, $middleware);
    }

    public function addHandler(CommandHandlerInterface $handler) 
    {
        array_push($this->publishHandlers, $handler);
    }

    public function addMockedHandler(): MockObject
    {
        $handler = $this->test
            ->getMockBuilder(CommandHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;
        array_push($this->publishHandlers, $handler);
        return $handler;
    }
}