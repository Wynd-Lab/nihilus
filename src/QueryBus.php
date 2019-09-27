<?php

namespace Nihilus\Handling;

use Nihilus\Handling\Exceptions\UnknowQueryException;

class QueryBus implements QueryBusInterface
{
    /**
     * @var DefaultHandlerFactory
     */
    private $factory;

    public function __construct()
    {
        $this->factory = new DefaultHandlerFactory();
    }

    public function execute(QueryInterface $query)
    {
        $class = HandlerRegistry::get(get_class($query));

        if (null === $class) {
            throw new UnknowQueryException($query);
        }

        $handler = $this->factory->create($class);

        return $handler->handle($query);
    }
}
