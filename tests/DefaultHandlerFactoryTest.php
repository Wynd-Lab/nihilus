<?php
xdebug_disable();

use PHPUnit\Framework\TestCase;
use Nihilus\Handling\DefaultHandlerFactory;
use Nihilus\Handling\HandlerInterface;
use Nihilus\Handling\QueryInterface;
use Nihilus\Tests\Context\TestHandler;
use Nihilus\Tests\Context\TestMessage;

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
        $handler = $handlerFactory->create(TestHandler::class);

        $reflectionClass = new ReflectionClass(TestHandler::class);

        $actual = $reflectionClass->isInstance($handler) && $reflectionClass->implementsInterface(HandlerInterface::class);
        // Assert
        $this->assertTrue($actual);
    }
}