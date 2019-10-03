<?php

namespace Nihilus\Handling;

interface PipelineResolverInterface
{
    /**
     * @return PipelineInterface[]
     */
    public function getGlobal(): array;
}
