<?php

namespace Nihilus\Handling;

interface CommandPipelineResolverInterface
{
    /**
     * @return CommandPipelineInterface[]
     */
    public function getGlobalCommandPipelines(): array;
}
