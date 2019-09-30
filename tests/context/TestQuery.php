<?php

namespace Nihilus\Tests\Context;

use Nihilus\Handling\QueryInterface;

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
