<?php

namespace Nihilus\Tests\Context;

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
