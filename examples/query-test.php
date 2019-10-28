<?php

use Nihilus\QueryBus;
use Nihilus\QueryHandlerInterface;
use Nihilus\QueryHandlerResolverInterface;
use Nihilus\QueryInterface;
use Nihilus\QueryMiddlewareInterface;
use Nihilus\QueryMiddlewareResolverInterface;

require './vendor/autoload.php';

class TQuery implements QueryInterface
{
    /**
     * @var string
     */
    public $test;
}

class TestHandler implements QueryHandlerInterface
{
    public function handle(QueryInterface $query): object
    {
        return new class() {
            public $property = 'property';
        };
    }
}

class Test1Middleware implements QueryMiddlewareInterface
{
    public function handle(QueryInterface $query, QueryHandlerInterface $next): object
    {
        var_dump('Return another object lol');

        //return $next->handle($query);
        return new class() {
            public $test = 'lol';
        };
    }
}

class Test2Middleware implements QueryMiddlewareInterface
{
    public function handle(QueryInterface $query, QueryHandlerInterface $next): object
    {
        var_dump('Before 2');
        $r = $next->handle($query);
        var_dump('After 2');

        return $r;
    }
}

class Test3Middleware implements QueryMiddlewareInterface
{
    public function handle(QueryInterface $query, QueryHandlerInterface $next): object
    {
        var_dump('Before 3');
        $r = $next->handle($query);
        var_dump('After 3');

        return $r;
    }
}

class QueryHandlerResolver implements QueryHandlerResolverInterface
{
    public function get(QueryInterface $query): QueryHandlerInterface
    {
        return new TestHandler();
    }
}

class MiddlewareResolver implements QueryMiddlewareResolverInterface
{
    public function get(QueryInterface $query): array
    {
        return [new Test1Middleware(), new Test2Middleware(), new Test3Middleware()];
    }
}

$queryBus = new QueryBus(new QueryHandlerResolver(), new MiddlewareResolver());
$result = $queryBus->execute(new TQuery());
var_dump($result);
