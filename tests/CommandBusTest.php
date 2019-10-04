<?php

use Nihilus\Handling\CommandBus;
use Nihilus\Handling\CommandHandlerInterface;
use Nihilus\Handling\CommandHandlerResolverInterface;
use Nihilus\Handling\CommandInterface;
use Nihilus\Handling\CommandPipelineInterface;
use Nihilus\Handling\Exceptions\UnknowCommandException;
use Nihilus\Handling\PipelineResolverInterface;
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
    private $handler;

    /**
     * @var CommandHandlerResolverInterface
     */
    private $commandHandlerResolver;

    /**
     * @var CommandHandlerInterface
     */
    private $commandHandlerResolverReturn;

    /**
     * @var PipelineResolverInterface
     */
    private $pipelineResolver;

    /**
     * @var CommandPipelineInterface[]
     */
    private $pipelineResolverReturn;

    /**
     * @var CommandPipelineInterface
     */
    private $pipeline;

    public function setUp()
    {
        $command = new class() implements CommandInterface {
        };

        $this->command = $command;

        $this->handler = $this
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

        $this->commandHandlerResolverReturn = $this->handler;

        $this->pipeline = $this
            ->getMockBuilder(CommandPipelineInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->pipelineResolver = $this
            ->getMockBuilder(PipelineResolverInterface::class)
            ->setMethods((['getGlobalCommandPipelines']))
            ->getMock()
        ;

        $this->pipelineResolver
            ->method('getGlobalCommandPipelines')
            ->will($this->returnCallback(
                function () {
                    return $this->pipelineResolverReturn;
                }
            ))
        ;

        $this->pipelineResolverReturn = [];
    }

    /**
     * @test
     */
    public function shouldHandleCommandWhenExecuteACommand()
    {
        // Arrange
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->pipelineResolver);

        // Assert
        $this->handler
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

        $commandBus = new CommandBus($this->commandHandlerResolver, $this->pipelineResolver);

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
        $this->pipelineResolverReturn = [$this->pipeline];
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->pipelineResolver);

        // Assert
        $this->pipeline
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
        $this->pipelineResolverReturn = [new class() implements CommandPipelineInterface {
            public function handle(CommandInterface $command, CommandHandlerInterface $next): void
            {
                $next->handle($command);
            }
        }];
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->pipelineResolver);

        $this->handler
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
        $this->pipelineResolverReturn = [new class() implements CommandPipelineInterface {
            public function handle(CommandInterface $command, CommandHandlerInterface $next): void
            {
            }
        }];
        $commandBus = new CommandBus($this->commandHandlerResolver, $this->pipelineResolver);

        $this->handler
            ->expects($this->never())
            ->method('handle')
            ->with($this->command)
        ;

        // Act
        $commandBus->execute($this->command);
    }
}
