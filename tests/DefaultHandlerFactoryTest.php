<?php
xdebug_disable();

use PHPUnit\Framework\TestCase;
use Sypontor\Nihilus\DefaultHandlerFactory;
use Sypontor\Nihilus\HandlerInterface;
use Sypontor\Nihilus\CommandInterface;

final class HandlerFactoryTest extends TestCase 
{
    /**
     * @test
     */
    public function shouldReturnNewQueryHandler_whenCreateAHandlerWithAQuery()
    {
        // Arrange
        $handlerFactory = new DefaultHandlerFactory();

        // Act
        $handler = $handlerFactory->create(TestCommandHandler::class);

        $reflectionClass = new ReflectionClass(TestCommandHandler::class);

        $actual = $reflectionClass->isInstance($handler) && $reflectionClass->implementsInterface(HandlerInterface::class);
        // Assert
        $this->assertTrue($actual);
    }
}

class TestCommandHandler implements HandlerInterface
{
    public function handle(CommandInterface $command) 
    {

    }
}