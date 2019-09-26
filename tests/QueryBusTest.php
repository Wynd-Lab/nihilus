<?php

use PHPUnit\Framework\TestCase;
use Nihilus\Handling\HandlerRegistry;
use Nihilus\Handling\HandlerInterface;
use Nihilus\Handling\QueryInterface;
use Nihilus\Handling\QueryBus;
use Nihilus\Tests\Context\TestHandler;
use Nihilus\Tests\Context\TestMessage;

final class QueryBusTest extends TestCase
{
    /**
     * @test
     */
    public function shouldHandleQuery_whenExecuteAQuery()
    {
        // Arrange
        $expected = "test";
        HandlerRegistry::add(TestCommand::class, TestHandler::class);
        $queryBus = new QueryBus();

        // Act
        $actual = $queryBus->execute(new TestMessage($expected));

        // Assert
        $this->assertEquals($actual, $expected);
    }
}