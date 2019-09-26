<?php

use PHPUnit\Framework\TestCase;
use Nihilus\Handling\HandlerInterface;
use Nihilus\Handling\QueryInterface;
use Nihilus\Handling\HandlerRegistry;
use Nihilus\Tests\Context\TestHandler;
use Nihilus\Tests\Context\TestMessage;

class HandlerRegistryTest extends TestCase
{
    /**
     * @test
     */
    function shouldReturnHandlerClass_whenCallGetMethodWithAQueryClass()
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