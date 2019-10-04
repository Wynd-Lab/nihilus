<?php

namespace Nihilus\Handling;

interface PipelineResolverInterface
{
    /**
     * @return CommandPipelineInterface[]
     */
    public function getGlobalCommandPipelines(): array;
}
