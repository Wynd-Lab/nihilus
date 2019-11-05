<?php

namespace Nihilus\Tests;

use Nihilus\QueryInterface;
use Nihilus\UnknowQueryException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class UnknowQueryExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function Given_AQuery_When_CreateAnUnknowQueryException_Then_TheExceptionMessageContainsInformationAboutTheQuery()
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