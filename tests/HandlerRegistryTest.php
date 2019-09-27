<?php

use Nihilus\Handling\HandlerRegistry;
use Nihilus\Tests\Context\TestHandler;
use Nihilus\Tests\Context\TestMessage;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HandlerRegistryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnHandlerClassWhenCallGetMethodWithAQueryClass()
    {
        // Arrange
        $messageClass = TestMessage::class;
        $expected = TestHandler::class;
        HandlerRegistry::add($messageClass, $expected);

        // Act
        $actual = HandlerRegistry::get($messageClass);

        // Assert
        $this->assertEquals($actual, $expected);
    }
}
