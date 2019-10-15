<?php

declare(strict_types=1);

namespace Nihilus;

interface CommandPipelineResolverInterface
{
    /**
     * @return CommandPipelineInterface[]
     */
    public function getGlobals(): array;
}
