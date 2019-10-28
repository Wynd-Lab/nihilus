<?php

use Nihilus\PublishCommandException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class PublishCommandExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function Given_Exceptions_When_CreateAPublishCommandException_Then_TheExceptionsCanBeRetrieveInThePublishCommandExceptionInstance()
    {
        // Arrange
        $expected = [new Exception(uniqid()), new Exception(uniqid())];

        // Act
        $publishCommandException = new PublishCommandException($expected);
        $actual = $publishCommandException->getHandlerExceptions();

        // Assert
        $this->assertEquals($expected, $actual);
    }
}
