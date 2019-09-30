<?php

use Nihilus\Handling\Exceptions\UnknowQueryException;
use Nihilus\Handling\QueryBus;
use Nihilus\Tests\Context\TestQuery;
use Nihilus\Tests\Context\TestQueryHandler;
use Nihilus\Tests\Context\TestQueryHandlerResolver;
use Nihilus\Tests\Context\TestQueryReadModel;
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
        $value = 'test';
        $expected = new TestQueryReadModel($value);

        $handlerResolver = new TestQueryHandlerResolver();
        $handlerResolver->add(TestQuery::class, TestQueryHandler::class);
        $queryBus = new QueryBus($handlerResolver);

        // Act
        $actual = $queryBus->execute(new TestQuery($value));

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
        $queryBus = new QueryBus(new TestQueryHandlerResolver());
        $queryBus->execute(new TestQuery(''));
    }
}
