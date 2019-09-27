<?php

namespace Nihilus\Tests\Context;

use Nihilus\Handling\QueryInterface;

class TestMessage implements QueryInterface
{
    private $prop;

    public function __construct(string $value)
    {
        $this->{prop} = $value;
    }

    public function getProp(): string
    {
        return $this->{prop};
    }
}
