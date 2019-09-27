<?php

use Nihilus\Handling\Exceptions\UnknowQueryException;
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
    protected function tearDown()
    {
        $property = new ReflectionProperty(HandlerRegistry::class, 'array');
        $property->setAccessible(true);
        $property->setValue(null, []);
        $property->setAccessible(false);
    }

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

    /**
     * @test
     */
    public function shouldThrowWhenHandlerIsNotFound()
    {
        // Arrange
        $this->expectException(UnknowQueryException::class);

        // Act
        $queryBus = new QueryBus();
        $queryBus->execute(new TestMessage(''));
    }
}
