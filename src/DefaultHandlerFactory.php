<?php
declare(strict_types=1);

namespace Sypontor\Nihilus;

final class DefaultHandlerFactory
{
    public function create(string $class)
    {
        $reflectionClass = new \ReflectionClass($class);
        return $reflectionClass->newInstance();
    }
}
