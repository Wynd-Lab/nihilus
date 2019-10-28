<?php

use Nihilus\QueryInterface;
use Nihilus\UnknowQueryException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UnknowQueryExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldHaveAnExplicitMessageWhenCreateANewInstance()
    {
        // Arrange
        $query = new class() implements QueryInterface {};
        $class = get_class($query);
        $expected = "Unkow query: {$class}";
        
        // Act
        $exception = new UnknowQueryException($query);
        $actual = $exception->getMessage();

        // Assert
        $this->assertEquals($actual, $expected);

    }
}