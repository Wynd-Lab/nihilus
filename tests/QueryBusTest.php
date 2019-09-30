<?php

use Nihilus\Handling\Exceptions\UnknowQueryException;
use Nihilus\Handling\QueryBus;
use Nihilus\Handling\QueryHandlerInterface;
use Nihilus\Handling\QueryHandlerResolverInterface;
use Nihilus\Handling\QueryInterface;
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
    }

    /**
     * @test
     */
    public function shouldHandleQueryWhenExecuteAQuery()
    {
        // Arrange
        $expected = new TestQueryReadModel($this->uid);

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->query)
            ->willReturn($expected)
        ;

        $this->queryHandlerResolver
            ->method('get')
            ->with($this->query)
            ->willReturn($this->handler)
        ;

        $queryBus = new QueryBus($this->queryHandlerResolver);

        // Act
        $actual = $queryBus->execute($this->query);

        // Assert
        $this->assertEquals($actual, $expected);
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

        $this->expectException(UnknowQueryException::class);

        // Act
        $queryBus = new QueryBus($this->queryHandlerResolver);
        $queryBus->execute($query);
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
