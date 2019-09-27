<?php

namespace Nihilus\Handling\Exceptions;

use Nihilus\Handling\QueryInterface;

class UnknowQueryException extends \Exception
{
    public function __construct(QueryInterface $query)
    {
        $class = get_class($query);
        parent::__construct("Unkow query: {$class}");
    }
}
