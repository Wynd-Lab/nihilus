<?php

use Nihilus\Handling\CommandBus;
use Nihilus\Handling\CommandHandlerInterface;
use Nihilus\Handling\CommandHandlerResolverInterface;
use Nihilus\Handling\CommandInterface;
use Nihilus\Handling\CommandPipelineInterface;
use Nihilus\Handling\CommandPipelineResolverInterface;
use Nihilus\Handling\Exceptions\UnknowCommandException;
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
     * @var CommandPipelineResolverInterface
     */
    private $commandPipelineResolver;

    /**
     * @var CommandPipelineInterface[]
     */
    private $commandPipelineResolverReturn;

    /**
     * @var CommandPipelineInterface
     */
    private $commandPipeline;

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

        $this->commandPipeline = $this
            ->getMockBuilder(CommandPipelineInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->commandPipelineResolver = $this
            ->getMockBuilder(CommandPipelineResolverInterface::class)
            ->setMethods((['getGlobals']))
            ->getMock()
        ;

        $this->commandPipelineResolver
            ->method('getGlobals')
            ->will($this->returnCallback(
                function () {
                    return $this->commandPipelineResolverReturn;
                }
            ))
        ;

        $this->commandPipelineResolverReturn = [];
    }

    /**
     * @test
     */
    public function shouldHandleCommandWhenExecuteACommand()
    {
        // Arrange
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->commandPipelineResolver);

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

        $commandBus = new CommandBus($this->commandHandlerResolver, $this->commandPipelineResolver);

        // Assert
        $this->expectException(UnknowCommandException::class);

        // Act
        $commandBus->execute($command);
    }

    /**
     * @test
     */
    public function shouldExecutePipelineWhenHandleACommand()
    {
        // Arrange
        $this->commandPipelineResolverReturn = [$this->commandPipeline];
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->commandPipelineResolver);

        // Assert
        $this->commandPipeline
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
    public function shouldHandleCommandWhenPipelineDontBreakTheExecutionFlow()
    {
        // Arrange
        $this->commandPipelineResolverReturn = [new class() implements CommandPipelineInterface {
            public function handle(CommandInterface $command, CommandHandlerInterface $next): void
            {
                $next->handle($command);
            }
        }];
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->commandPipelineResolver);

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
    public function shouldBreakTheExecutionFlowWhenPipelineDontHandleCommandWithTheNextPipeline()
    {
        // Arrange
        $this->commandPipelineResolverReturn = [new class() implements CommandPipelineInterface {
            public function handle(CommandInterface $command, CommandHandlerInterface $next): void
            {
            }
        }];
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->commandPipelineResolver);

        $this->commandHandler
            ->expects($this->never())
            ->method('handle')
            ->with($this->command)
        ;

        // Act
        $commandBus->execute($this->command);
    }
}
