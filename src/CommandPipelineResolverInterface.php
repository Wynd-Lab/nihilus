<?php

namespace Nihilus;

interface CommandPipelineResolverInterface
{
    /**
     * @return CommandPipelineInterface[]
     */
    public function getGlobals(): array;
}
