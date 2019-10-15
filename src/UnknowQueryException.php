<?php

declare(strict_types=1);

namespace Nihilus;

class UnknowQueryException extends \Exception
{
    public function __construct(QueryInterface $query)
    {
        $class = get_class($query);
        parent::__construct("Unkow query: {$class}");
    }
}
