<?php

xdebug_disable();

use Nihilus\Handling\DefaultHandlerFactory;
use Nihilus\Handling\HandlerInterface;
use Nihilus\Tests\Context\TestHandler;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class HandlerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnNewQueryHandlerWhenCreateAHandlerWithAQuery()
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
