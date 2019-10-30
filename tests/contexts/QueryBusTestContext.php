<?php

namespace Nihilus\Tests;

use Nihilus\QueryHandlerInterface;
use Nihilus\QueryHandlerResolverInterface;
use Nihilus\QueryInterface;
use Nihilus\QueryMiddlewareInterface;
use Nihilus\QueryMiddlewareResolverInterface;
use PHPUnit\Framework\TestCase;

final class QueryBusTestContext
{
    /**
     * @var string
     */
    private $uid;

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
     * @var QueryHandlerResolverInterface
     */
    private $queryHandlerResolver;

    /**
     * @var QueryMiddlewareResolverInterface
     */
    private $queryMiddlewareResolver;

    /**
     * @var QueryMiddlewareInterface
     */
    private $queryMiddleware;

    /** 
     * @var QueryMiddlewareInterface[]
     */
    private $queryMiddlewareResolverReturn;

    /**
     * @var TestCase
     */
    private $test;

    public function __construct(TestCase $test)
    {
        $this->uid = uniqid();
        $this->test = $test;
    }

    /**
     * Get the value of query
     *
     * @return  QueryInterface
     */ 
    public function getQuery()
    {
        return $this->query;
    }


    /**
     * Get the value of queryResult
     *
     * @return  object
     */ 
    public function getQueryResult()
    {
        return $this->queryResult;
    }

    /**
     * Get the value of queryHandler
     *
     * @return  QueryHandlerInterface
     */ 
    public function getQueryHandler()
    {
        return $this->queryHandler;
    }

    /**
     * Get the value of queryHandlerResolverReturn
     *
     * @return  QueryHandlerInterface
     */ 
    public function getQueryHandlerResolverReturn()
    {
        return $this->queryHandlerResolverReturn;
    }

    /**
     * Get the value of queryHandlerResolver
     *
     * @return  QueryHandlerResolverInterface
     */ 
    public function getQueryHandlerResolver()
    {
        return $this->queryHandlerResolver;
    }

    /**
     * Get the value of queryMiddlewareResolver
     *
     * @return  QueryMiddlewareResolverInterface
     */ 
    public function getQueryMiddlewareResolver()
    {
        return $this->queryMiddlewareResolver;
    }

    /**
     * Get the value of queryMiddlewareResolverReturn
     *
     * @return  QueryMiddlewareInterface[]
     */ 
    public function getQueryMiddlewareResolverReturn()
    {
        return $this->queryMiddlewareResolverReturn;
    }

    /**
     * Get the value of queryMiddleware
     *
     * @return  QueryMiddlewareInterface
     */ 
    public function getQueryMiddleware()
    {
        return $this->queryMiddleware;
    }

    public function setUpQueryResult(): void
    {
        $this->queryResult = new class($this->uid) {
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
    }

    public function setUpQuery()
    {
        $this->query = new class($this->uid) implements QueryInterface {
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
    }

    public function setUpHandler()
    {
        $this->queryHandler = $this->test
            ->getMockBuilder(QueryHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->queryHandler
            ->method('handle')
            ->with($this->query)
            ->willReturn($this->queryResult)
        ;

        $this->queryHandlerResolver = $this->test
            ->getMockBuilder(QueryHandlerResolverInterface::class)
            ->setMethods((['get']))
            ->getMock()
        ;

        $this->queryHandlerResolver
            ->method('get')
            ->will($this->test
                ->returnCallback(
                    function($arg) {
                        if($arg === $this->query) {
                            return $this->queryHandler;
                        } else {
                            return null;
                        }
                    }
                )
            )
        ;
    }

    public function setUpMiddlewares()
    {
        $this->queryMiddleware = $this->test
            ->getMockBuilder(QueryMiddlewareInterface::class)
            ->setMethods(['handle'])
            ->getMock()
        ;

        $this->queryMiddleware
            ->method(('handle'))
            ->will($this->test
                ->returnCallback(
                    function($query, $next) {
                        return $next->handle($query);
                    }
                )
            )
        ;

        $this->queryMiddlewareResolver = $this->test
            ->getMockBuilder(QueryMiddlewareResolverInterface::class)
            ->setMethods((['get']))
            ->getMock()
        ;

        $this->queryMiddlewareResolver
            ->method('get')
            ->will($this->test->returnCallback(
                function ($arg) {
                    if($arg === $this->query) {
                        return $this->queryMiddlewareResolverReturn;
                    } else {
                        return [];
                    }
                }
            ))
        ;

        $this->queryMiddlewareResolverReturn = [$this->queryMiddleware];
    }

    public function addMiddleware(QueryMiddlewareInterface $middleware) 
    {
        array_push($this->queryMiddlewareResolverReturn, $middleware);
    }
}