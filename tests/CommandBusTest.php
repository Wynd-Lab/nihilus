<?php

use Nihilus\Handling\CommandBus;
use Nihilus\Handling\CommandHandlerInterface;
use Nihilus\Handling\CommandHandlerResolverInterface;
use Nihilus\Handling\CommandInterface;
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

        $this->commandHandlerResolver
            ->method('get')
            ->with($this->command)
            ->willReturn($this->handler)
        ;
    }

    /**
     * @test
     */
    public function shouldHandleCommandWhenExecuteAQuery()
    {
        // Arrange
        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->command)
        ;

        $commandBus = new CommandBus($this->commandHandlerResolver);

        // Act
        $commandBus->execute($this->command);
    }
}

class TestCommand implements CommandInterface
{
}
