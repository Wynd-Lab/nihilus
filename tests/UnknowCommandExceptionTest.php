<?php

namespace Nihilus\Tests;

use Nihilus\CommandInterface;
use Nihilus\UnknowCommandException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class UnknowCommandExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function Given_ACommand_When_CreateAnUnknowCommandException_Then_TheExceptionMessageContainsInformationAboutTheCommand()
    {
        // Arrange
        $command = new class() implements CommandInterface {};
        $class = get_class($command);
        $expected = "Unkow command: {$class}";
        
        // Act
        $exception = new UnknowCommandException($command);
        $actual = $exception->getMessage();

        // Assert
        $this->assertEquals($actual, $expected);

    }
}