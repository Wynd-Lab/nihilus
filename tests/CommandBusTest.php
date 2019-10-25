<?php

use Nihilus\CommandBus;
use Nihilus\CommandHandlerInterface;
use Nihilus\CommandHandlerResolverInterface;
use Nihilus\CommandInterface;
use Nihilus\CommandMiddlewareInterface;
use Nihilus\CommandMiddlewareResolverInterface;
use Nihilus\UnknowCommandException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CommandBusTest extends TestCase
{
    /**
     * @var CommandInterface
     */
    private $command;

    /**
     * @var CommandHandlerInterface
     */
    private $commandHandler;

    /**
     * @var CommandHandlerResolverInterface
     */
    private $commandHandlerResolver;

    /**
     * @var CommandHandlerInterface
     */
    private $commandHandlerResolverReturn;

    /**
     * @var CommandMiddlewareResolverInterface
     */
    private $commandMiddlewareResolver;

    /**
     * @var CommandMiddlewareInterface[]
     */
    private $commandMiddlewareResolverReturn;

    /**
     * @var CommandMiddlewareInterface
     */
    private $commandMiddleware;

    public function setUp()
    {
        $command = new class() implements CommandInterface {
        };

        $this->command = $command;

        $this->commandHandler = $this
            ->getMockBuilder(CommandHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->commandHandlerResolver = $this
            ->getMockBuilder(CommandHandlerResolverInterface::class)
            ->setMethods((['get']))
            ->getMock()
        ;

        $this->commandHandlerResolver
            ->method('get')
            ->will($this->returnCallback(
                function () {
                    return $this->commandHandlerResolverReturn;
                }
            ))
        ;

        $this->commandHandlerResolverReturn = $this->commandHandler;

        $this->commandMiddleware = $this
            ->getMockBuilder(CommandMiddlewareInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->commandMiddlewareResolver = $this
            ->getMockBuilder(CommandMiddlewareResolverInterface::class)
            ->setMethods((['get']))
            ->getMock()
        ;

        $this->commandMiddlewareResolver
            ->method('get')
            ->with($this->command)
            ->will($this->returnCallback(
                function () {
                    return $this->commandMiddlewareResolverReturn;
                }
            ))
        ;

        $this->commandMiddlewareResolverReturn = [];
    }

    /**
     * @test
     */
    public function shouldHandleCommandWhenExecuteACommand()
    {
        // Arrange
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->commandMiddlewareResolver);

        // Assert
        $this->commandHandler
            ->expects($this->once())
            ->method('handle')
            ->with($this->command)
        ;

        // Act
        $commandBus->execute($this->command);
    }

    /**
     * @test
     */
    public function shouldThrowWhenHandlerIsNotFound()
    {
        // Arrange
        $this->commandHandlerResolverReturn = null;
        $command = new class() implements CommandInterface {
        };

        $commandBus = new CommandBus($this->commandHandlerResolver, $this->commandMiddlewareResolver);

        // Assert
        $this->expectException(UnknowCommandException::class);

        // Act
        $commandBus->execute($command);
    }

    /**
     * @test
     */
    public function shouldExecuteMiddlewareWhenHandleACommand()
    {
        // Arrange
        $this->commandMiddlewareResolverReturn = [$this->commandMiddleware];
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->commandMiddlewareResolver);

        // Assert
        $this->commandMiddleware
            ->expects($this->once())
            ->method('handle')
            ->with($this->command)
        ;

        // Act
        $commandBus->execute($this->command);
    }

    /**
     * @test
     */
    public function shouldHandleCommandWhenMiddlewareDontBreakTheExecutionFlow()
    {
        // Arrange
        $this->commandMiddlewareResolverReturn = [new class() implements CommandMiddlewareInterface {
            public function handle(CommandInterface $command, CommandHandlerInterface $next): void
            {
                $next->handle($command);
            }
        }];
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->commandMiddlewareResolver);

        $this->commandHandler
            ->expects($this->once())
            ->method('handle')
            ->with($this->command)
        ;

        // Act
        $commandBus->execute($this->command);
    }

    /**
     * @test
     */
    public function shouldBreakTheExecutionFlowWhenMiddlewareDontHandleCommandWithTheNextMiddleware()
    {
        // Arrange
        $this->commandMiddlewareResolverReturn = [new class() implements CommandMiddlewareInterface {
            public function handle(CommandInterface $command, CommandHandlerInterface $next): void
            {
            }
        }];
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->commandMiddlewareResolver);

        $this->commandHandler
            ->expects($this->never())
            ->method('handle')
            ->with($this->command)
        ;

        // Act
        $commandBus->execute($this->command);
    }
}
