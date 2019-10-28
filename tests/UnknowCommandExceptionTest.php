<?php

use Nihilus\CommandInterface;
use Nihilus\UnknowCommandException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UnknowCommandExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldHaveAnExplicitMessageWhenCreateANewInstance()
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