<?php
declare(strict_types=1);

namespace Nihilus\Handling;

final class DefaultHandlerFactory
{
    public function create(string $handlerClass)
    {
        $reflectionClass = new \ReflectionClass($handlerClass);
        return $reflectionClass->newInstance();
    }
}
