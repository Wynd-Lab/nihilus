<?php

use Nihilus\Handling\HandlerRegistry;
use Nihilus\Handling\QueryBus;
use Nihilus\Tests\Context\TestHandler;
use Nihilus\Tests\Context\TestMessage;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class QueryBusTest extends TestCase
{
    /**
     * @test
     */
    public function shouldHandleQueryWhenExecuteAQuery()
    {
        // Arrange
        $expected = 'test';
        HandlerRegistry::add(TestCommand::class, TestHandler::class);
        $queryBus = new QueryBus();

        // Act
        $actual = $queryBus->execute(new TestMessage($expected));

        // Assert
        $this->assertEquals($actual, $expected);
    }

    public function shouldThrow_whenHandlerIsNotFound()
    {
    }
}
