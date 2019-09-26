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
        $this->$factory = new DefaultHandlerFactory();
    }

    public function execute(QueryInterface $command)
    {
        $handlerClass = HandlerRegistry::get(get_class($command));
        $handler = $this->$factory->create($handlerClass);
        return $handler->handle($command);
    } 
}