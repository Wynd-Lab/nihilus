<?php

namespace Nihilus\Handling;

use Nihilus\Handling\QueryInterface;
use Nihilus\Handling\QueryBusInterface;
use Nihilus\Handling\DefaultHandlerFactory;

class QueryBus implements QueryBusInterface
{
    private $factory;

    function __construct() 
    {
        $this->factory = new DefaultHandlerFactory();
    }

    public function execute(QueryInterface $query)
    {
        $class = HandlerRegistry::get(get_class($query));
        $handler = $this->factory->create($class);
        return $handler->handle($query);
    } 
}