<?php

use Nihilus\Handling\Exceptions\UnknowQueryException;
use Nihilus\Handling\QueryBus;
use Nihilus\Handling\QueryHandlerInterface;
use Nihilus\Handling\QueryHandlerResolverInterface;
use Nihilus\Handling\QueryInterface;
use Nihilus\Handling\QueryPipelineInterface;
use Nihilus\Handling\QueryPipelineResolverInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class QueryBusTest extends TestCase
{
    /**
     * @var string
     */
    private $uid;

    /**
     * @var QueryHandlerResolverInterface
     */
    private $queryHandlerResolver;

    /**
     * @var TestQuery
     */
    private $query;

    private $handler;

    /**
     * @var QueryPipelineResolverInterface
     */
    private $queryPipelineResolver;

    /**
     * @var QueryPipelineInterface[]
     */
    private $queryPipelineResolverReturn;

    /**
     * @var QueryPipelineInterface
     */
    private $queryPipeline;

    public function setUp()
    {
        $this->uid = uniqid();
        $this->query = new TestQuery($this->uid);

        $this->handler = $this
            ->getMockBuilder(QueryHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->queryHandlerResolver = $this
            ->getMockBuilder(QueryHandlerResolverInterface::class)
            ->setMethods((['get']))
            ->getMock()
        ;

        $this->queryPipeline = $this
            ->getMockBuilder(QueryPipelineInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->queryPipelineResolver = $this
            ->getMockBuilder(QueryPipelineResolverInterface::class)
            ->setMethods((['getGlobals']))
            ->getMock()
        ;

        $this->queryPipelineResolver
            ->method('getGlobals')
            ->will($this->returnCallback(
                function () {
                    return $this->queryPipelineResolverReturn;
                }
            ))
        ;

        $this->queryPipelineResolverReturn = [];
    }

    /**
     * @test
     */
    public function shouldHandleQueryWhenExecuteAQuery()
    {
        // Arrange
        $expected = new TestQueryReadModel($this->uid);

        $this->queryHandlerResolver
            ->method('get')
            ->with($this->query)
            ->willReturn($this->handler)
        ;

        $this->handler
            ->method('handle')
            ->with($this->query)
            ->willReturn($expected)
        ;

        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryPipelineResolver);

        // Act
        $actual = $queryBus->execute($this->query);

        $this->assertEquals($actual, $expected);
    }

    /**
     * @test
     */
    public function shouldHandleQueryOnceWhenExecuteAQuery()
    {
        // Arrange
        $expected = new TestQueryReadModel($this->uid);

        $this->queryHandlerResolver
            ->method('get')
            ->with($this->query)
            ->willReturn($this->handler)
        ;

        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryPipelineResolver);

        // Assert
        $this->handler
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
    public function shouldThrowWhenHandlerIsNotFound()
    {
        // Arrange
        $query = new UnknowTestQuery();

        $this->queryHandlerResolver
            ->method('get')
            ->with($query)
            ->willReturn(null)
        ;

        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryPipelineResolver);

        // Assert
        $this->expectException(UnknowQueryException::class);

        // Act
        $queryBus->execute($query);
    }

    /**
     * @test
     */
    public function shouldExecutePipelineWhenHandleAQuery()
    {
        // Arrange
        $expected = new TestQueryReadModel($this->uid);
        $this->queryPipelineResolverReturn = [$this->queryPipeline];
        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryPipelineResolver);

        $this->queryHandlerResolver
            ->method('get')
            ->with($this->query)
            ->willReturn($this->handler)
        ;

        $this->handler
            ->method('handle')
            ->with($this->query)
            ->willReturn($expected)
        ;

        // Assert
        $this->queryPipeline
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
    public function shouldHandleQueryWhenPipelineDontBreakTheExecutionFlow()
    {
        // Arrange
        $expected = new TestQueryReadModel($this->uid);
        $this->queryPipelineResolverReturn = [new class() implements QueryPipelineInterface {
            public function handle(QueryInterface $query, QueryHandlerInterface $next): object
            {
                return $next->handle($query);
            }
        }];

        $this->queryHandlerResolver
            ->method('get')
            ->with($this->query)
            ->willReturn($this->handler)
        ;

        $this->handler
            ->method('handle')
            ->with($this->query)
            ->willReturn($expected)
        ;

        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryPipelineResolver);

        $this->handler
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
    public function shouldBreakTheExecutionFlowWhenPipelineDontHandleQueryWithTheNextPipeline()
    {
        // Arrange
        $this->queryPipelineResolverReturn = [new class() implements QueryPipelineInterface {
            public function handle(QueryInterface $query, QueryHandlerInterface $next): object
            {
                return new class() {
                };
            }
        }];
        $queryBus = new QueryBus($this->queryHandlerResolver, $this->queryPipelineResolver);

        $this->queryHandlerResolver
            ->method('get')
            ->with($this->query)
            ->willReturn($this->handler)
        ;

        $this->handler
            ->expects($this->never())
            ->method('handle')
            ->with($this->query)
        ;

        // Act
        $queryBus->execute($this->query);
    }
}

class TestQuery implements QueryInterface
{
    private $prop;

    public function __construct(string $value)
    {
        $this->prop = $value;
    }

    public function getProp(): string
    {
        return $this->prop;
    }
}

class TestQueryReadModel
{
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
}

class UnknowTestQuery implements QueryInterface
{
}
