<?php

use Nihilus\Handling\CommandBus;
use Nihilus\Handling\CommandHandlerInterface;
use Nihilus\Handling\CommandHandlerResolverInterface;
use Nihilus\Handling\CommandInterface;
use Nihilus\Handling\Exceptions\UnknowCommandException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class CommandBusTest extends TestCase
{
    /**
     * @var CommandHandlerResolverInterface
     */
    private $commandHandlerResolver;

    /**
     * @var TestCommand
     */
    private $command;

    private $handler;

    public function setUp()
    {
        $this->command = new TestCommand();

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
    }

    /**
     * @test
     */
    public function shouldHandleCommandWhenExecuteAQuery()
    {
        // Arrange
        $this->commandHandlerResolver
            ->method('get')
            ->with($this->command)
            ->willReturn($this->handler)
        ;

        $commandBus = new CommandBus($this->commandHandlerResolver);

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
        $command = new UnknowTestCommand();

        $this->commandHandlerResolver
            ->method('get')
            ->with($command)
            ->willReturn(null)
        ;

        $commandBus = new CommandBus($this->commandHandlerResolver);

        // Assert
        $this->expectException(UnknowCommandException::class);

        // Act
        $commandBus->execute($command);
    }
}

class TestCommand implements CommandInterface
{
}

class UnknowTestCommand implements CommandInterface
{
}
