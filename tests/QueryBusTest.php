<?php

use Nihilus\QueryBus;
use Nihilus\QueryHandlerInterface;
use Nihilus\QueryHandlerResolverInterface;
use Nihilus\QueryInterface;
use Nihilus\QueryMiddlewareInterface;
use Nihilus\QueryMiddlewareResolverInterface;
use Nihilus\UnknowQueryException;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class QueryBusTest extends TestCase
{
    /**
     * @var QueryInterface
     */
    private $query;

    /**
     * @var object
     */
    private $queryResult;

    /**
     * @var QueryHandlerInterface
     */
    private $queryHandler;

    /**
     * @var QueryHandlerInterface
     */
    private $queryHandlerResolverReturn;

    /**
     * @var QueryHandlerResolverInterface
     */
    private $queryHandlerResolver;

    /**
     * @var QueryMiddlewareResolverInterface
     */
    private $queryMiddlewareResolver;

    /**
     * @var QueryMiddlewareInterface[]
     */
    private $queryMiddlewareResolverReturn;

    /**
     * @var QueryMiddlewareInterface
     */
    private $queryMiddleware;

    public function setUp()
    {
        $uid = uniqid();
        $this->queryResult = new class($uid) {
            /**
             * @var string
             */
            private $value;

            public function __construct(string $value)
            {
                $this->value = $value;
            }

            public function getValue(): string
            {
                return $this->result;
            }
        };

        $this->query = new class($uid) implements QueryInterface {
            private $prop;

            public function __construct(string $value)
            {
                $this->prop = $value;
            }

            public function getProp(): string
            {
                return $this->prop;
            }
        };

        $this->queryHandler = $this
            ->getMockBuilder(QueryHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->queryHandlerResolver = $this
            ->getMockBuilder(QueryHandlerResolverInterface::class)
            ->setMethods((['get']))
            ->getMock()
        ;

        $this->queryHandlerResolver
            ->method('get')
            ->will($this->returnCallback(
                function () {
                    return $this->queryHandlerResolverReturn;
                }
            ))
        ;

        $this->queryHandlerResolverReturn = $this->queryHandler;

        $this->queryMiddleware = $this
            ->getMockBuilder(QueryMiddlewareInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->queryMiddlewareResolver = $this
            ->getMockBuilder(QueryMiddlewareResolverInterface::class)
            ->setMethods((['get']))
            ->getMock()
        ;

        $this->queryMiddlewareResolver
            ->method('get')
            ->with($this->query)
            ->will($this->returnCallback(
                function () {
                    return $this->queryMiddlewareResolverReturn;
                }
            ))
        ;

        $this->queryMiddlewareResolverReturn = [];
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithHandlers_When_AQueryIsExecuted_Then_AHandlerReturnSomething()
    {
        // Arrange
        $expected = $this->queryResult;

        $this->queryHandler
            ->method('handle')
            ->with($this->query)
            ->willReturn($expected)
        ;

        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryMiddlewareResolver);

        // Act
        $actual = $queryBus->execute($this->query);

        $this->assertEquals($actual, $expected);
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithHandlers_When_AQueryIsExecuted_Then_AHandlerHandleItOnce()
    {
        // Arrange
        $expected = $this->queryResult;

        $this->queryHandlerResolver
            ->method('get')
            ->with($this->query)
            ->willReturn($this->queryHandler)
        ;

        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryMiddlewareResolver);

        // Assert
        $this->queryHandler
            ->expects($this->once())
            ->method('handle')
            ->with($this->query)
            ->willReturn($expected)
        ;

        // Act
        $queryBus->execute($this->query);
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithoutHandlers_When_AQueryIsExecuted_Then_AnUnkowQueryExceptionIsThrow()
    {
        // Arrange
        $this->queryHandlerResolverReturn = null;
        $query = new class() implements QueryInterface {
        };

        $this->queryHandlerResolver
            ->method('get')
            ->with($query)
            ->willReturn(null)
        ;

        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryMiddlewareResolver);

        // Assert
        $this->expectException(UnknowQueryException::class);

        // Act
        $queryBus->execute($query);
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithHandlersAndMiddlewares_When_AQueryIsExecuted_Then_MiddlewaresAreExecuted()
    {
        // Arrange
        $this->queryMiddlewareResolverReturn = [$this->queryMiddleware];
        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryMiddlewareResolver);

        $this->queryHandlerResolver
            ->method('get')
            ->with($this->query)
            ->willReturn($this->queryHandler)
        ;

        $this->queryHandler
            ->method('handle')
            ->with($this->query)
            ->willReturn($this->queryResult)
        ;

        // Assert
        $this->queryMiddleware
            ->expects($this->once())
            ->method('handle')
            ->with($this->query)
        ;

        // Act
        $queryBus->execute($this->query);
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithHandlersAndMiddlewares_When_AQueryIsExecuted_Then_AHandlerHandledIt()
    {
        // Arrange
        $this->queryMiddlewareResolverReturn = [new class() implements QueryMiddlewareInterface {
            public function handle(QueryInterface $query, QueryHandlerInterface $next): object
            {
                return $next->handle($query);
            }
        }];

        $this->queryHandlerResolver
            ->method('get')
            ->with($this->query)
            ->willReturn($this->queryHandler)
        ;

        $this->queryHandler
            ->method('handle')
            ->with($this->query)
            ->willReturn($this->queryResult)
        ;

        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryMiddlewareResolver);

        $this->queryHandler
            ->expects($this->once())
            ->method('handle')
            ->with($this->query)
        ;

        // Act
        $queryBus->execute($this->query);
    }

    /**
     * @test
     */
    public function Given_AQueryBusWithHandlersAndMiddlewaresThatDontCallNextMiddleware_When_AQueryIsExecuted_Then_NoHandlerHandledIt()
    {
        // Arrange
        $this->queryMiddlewareResolverReturn = [new class() implements QueryMiddlewareInterface {
            public function handle(QueryInterface $query, QueryHandlerInterface $next): object
            {
                return new class() {
                };
            }
        }];
        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryMiddlewareResolver);

        $this->queryHandlerResolver
            ->method('get')
            ->with($this->query)
            ->willReturn($this->queryHandler)
        ;

        $this->queryHandler
            ->expects($this->never())
            ->method('handle')
            ->with($this->query)
        ;

        // Act
        $queryBus->execute($this->query);
    }
}
