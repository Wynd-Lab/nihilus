<?php

namespace Nihilus\Handling;

interface CommandBusInteface
{
    public function execute(CommandInterface $command): void;
}
