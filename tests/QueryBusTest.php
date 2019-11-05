<?php

namespace Nihilus\Tests;

use Nihilus\QueryBus;
use Nihilus\QueryBusInterface;
use Nihilus\QueryHandlerInterface;
use Nihilus\QueryHandlerResolverInterface;
use Nihilus\QueryInterface;
use Nihilus\QueryMiddlewareInterface;
use Nihilus\QueryMiddlewareResolverInterface;
use Nihilus\UnknowQueryException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class QueryBusTest extends TestCase
{
    /**
     * @var QueryBusTestContext
     */
    private $context;

    /**
     * @var QueryBusInterface
     */
    private $queryBus;

    public function setUp(): void
    {
        $this->context = new QueryBusTestContext($this);
        $this->context->setUpQueryResult();
        $this->context->setUpQuery();
        $this->context->setUpHandler();
        $this->context->setUpMiddlewares();
        $this->queryBus = new QueryBus(
            $this->context->getQueryHandlerResolver(), 
            $this->context->getQueryMiddlewareResolver()
        );
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithHandlers_When_AQueryIsExecuted_Then_AHandlerReturnSomething()
    {
        // Arrange
        $expected = $this->context->getQueryResult();

        // Act
        $actual = $this->queryBus->execute($this->context->getQuery());

        // Assert
        $this->assertEquals($actual, $expected);
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithHandlers_When_AQueryIsExecuted_Then_AHandlerHandleItOnce()
    {
        // Arrange
        $expected = $this->context->getQueryResult();

        // Assert
        $this->context
            ->getQueryHandler()
            ->expects($this->once())
            ->method('handle')
            ->with($this->context->getQuery())
            ->willReturn($expected)
        ;

        // Act
        $this->queryBus->execute($this->context->getQuery());
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithoutHandlers_When_AQueryIsExecuted_Then_AnUnkowQueryExceptionIsThrow()
    {
        // Arrange
        $query = new class() implements QueryInterface {

        };

        // Assert
        $this->expectException(UnknowQueryException::class);

        // Act
        $this->queryBus->execute($query);
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithHandlersAndMiddlewares_When_AQueryIsExecuted_Then_MiddlewaresAreExecuted()
    {
        // Assert
        $this->context
            ->getQueryMiddleware()
            ->expects($this->once())
            ->method('handle')
            ->with($this->context->getQuery())
        ;

        // Act
        $this->queryBus->execute($this->context->getQuery());
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithHandlersAndMiddlewares_When_AQueryIsExecuted_Then_AHandlerHandledIt()
    {
        // Assert
        $this->context
            ->getQueryHandler()
            ->expects($this->once())
            ->method('handle')
            ->with($this->context->getQuery())
        ;

        // Act
        $this->queryBus->execute($this->context->getQuery());
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithHandlersAndMiddlewaresThatDontCallNextMiddleware_When_AQueryIsExecuted_Then_NoHandlerHandledIt()
    {
        // Arrange
        $this->context->addMiddleware(new class() implements QueryMiddlewareInterface {
            public function handle(QueryInterface $query, QueryHandlerInterface $next): object
            {
                return new class() {
                };
            }
        });

        $this->context
            ->getQueryHandler()
            ->expects($this->never())
            ->method('handle')
            ->with($this->context->getQuery())
        ;

        // Act
        $this->queryBus->execute($this->context->getQuery());
    }
}
