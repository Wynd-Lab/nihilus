<?php

namespace Nihilus\Tests;

use Nihilus\CommandBus;
use Nihilus\CommandHandlerInterface;
use Nihilus\CommandHandlerResolverInterface;
use Nihilus\CommandInterface;
use Nihilus\CommandMiddlewareInterface;
use Nihilus\CommandMiddlewareResolverInterface;
use Nihilus\PublishCommandException;
use Nihilus\UnknowCommandException;
use PHPUnit\Framework\TestCase;
use Exception;

/**
 * @internal
 */
final class CommandBusTest extends TestCase
{
    /**
     * @var CommandBusTestContext
     */
    private $context;

    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    public function setUp(): void
    {
        $this->context = new CommandBusTestContext($this);
        $this->context->setUpCommand();
        $this->context->setUpHandler();
        $this->context->setUpMiddlewares();
        $this->commandBus = new CommandBus(
            $this->context->getCommandHandlerResolver(), 
            $this->context->getCommandMiddlewareResolver()
        );
    }

    /**
     * @test
     */
    public function Given_ACommandBusWithHandlers_When_ACommandIsExecuted_Then_AHandlerHandledIt()
    {
        // Assert
        $this->context
            ->getCommandHandler()
            ->expects($this->once())
            ->method('handle')
            ->with($this->context->getCommand())
        ;

        // Act
        $this->commandBus->execute($this->context->getCommand());
    }

    /**
     * @test
     */
    public function Given_ACommandBusWithoutHandlers_When_ACommandIsExecuted_Then_AnUnkowCommandExceptionIsThrow()
    {
        // Arrange
        $command = new class() implements CommandInterface {
        };

        // Assert
        $this->expectException(UnknowCommandException::class);

        // Act
        $this->commandBus->execute($command);
    }

    /**
     * @test
     */
    public function Given_ACommandBusWithHandlersAndMiddlewares_When_ACommandIsExecuted_Then_MiddlewaresAreExecuted()
    {
        // Assert
        $this->context
            ->getCommandMiddleware()
            ->expects($this->once())
            ->method('handle')
            ->with($this->context->getCommand())
        ;

        // Act
        $this->commandBus->execute($this->context->getCommand());
    }

    /**
     * @test
     */
    public function Given_ACommandBusWithHandlersAndMiddlewares_When_ACommandIsExecuted_Then_AHandlerHandledIt()
    {
        // Arrange
        $this->context->addMiddleware(new class() implements CommandMiddlewareInterface {
            public function handle(CommandInterface $command, CommandHandlerInterface $next): void
            {
                $next->handle($command);
            }
        });

        $this->context
            ->getCommandHandler()
            ->expects($this->once())
            ->method('handle')
            ->with($this->context->getCommand())
        ;

        // Act
        $this->commandBus->execute($this->context->getCommand());
    }

    /**
     * @test
     */
    public function Given_ACommandBusWithHandlersAndMiddlewaresThatDontCallNextMiddleware_When_ACommandIsExecuted_Then_NoHandlerHandledIt()
    {
        // Arrange
        $this->context->addMiddleware(new class() implements CommandMiddlewareInterface {
            public function handle(CommandInterface $command, CommandHandlerInterface $next): void
            {
            }
        });
        
        $this->context
            ->getCommandHandler()
            ->expects($this->never())
            ->method('handle')
            ->with($this->context->getCommand())
        ;

        // Act
        $this->commandBus->execute($this->context->getCommand());
    }

    /**
     * @test
     */
    public function Given_ACommandBusWithoutHandlers_When_ACommandIsPublished_Then_AnUnkowCommandExceptionIsThrow()
    {
        // Arrange
        $command = new class() implements CommandInterface {
        };

        // Assert
        $this->expectException(UnknowCommandException::class);

        // Act
        $this->commandBus->publish($command);
    }

    /**
     * @test
     */
    public function Given_ACommandBusWithHandlers_When_ACommandIsPublished_Then_MultipleHandlersHandledIt()
    {
        // Arrange
        $handler = $this->context->addMockedHandler();

        // Assert
        $this->context
            ->getCommandHandler()
            ->expects($this->once())
            ->method('handle')
            ->with($this->context->getCommand())
        ;

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->context->getCommand())
        ;

        // Act
        $this->commandBus->publish($this->context->getCommand());
    }

    /**
     * @test
     */
    public function Given_ACommandBusWithHandlersThatThrowException_When_ACommandIsPublished_Then_APublishCommandExceptionIsThrow()
    {
        // Arrange
        $this->context
            ->getCommandHandler()
            ->method('handle')
            ->will($this->throwException(new Exception(uniqid())))
        ;

        // Assert
        $this->expectException(PublishCommandException::class);

        // Act
        $this->commandBus->publish($this->context->getCommand());
    }

    /**
     * @test
     */
    public function Given_ACommandBusWithHandlersThatThrowException_When_ACommandIsPublished_Then_NextHandlerCanHandleTheCommand()
    {
        // Arrange
        $expected = new Exception(uniqid());

        $handler = $this->context->addMockedHandler();

        $this->context
            ->getCommandHandler()
            ->method('handle')
            ->with($this->context->getCommand())
            ->will($this->throwException($expected))
        ;

        // Assert
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->context->getCommand())
        ;

        $this->expectException(PublishCommandException::class);

        // Act
        $this->commandBus->publish($this->context->getCommand());
    }
}
