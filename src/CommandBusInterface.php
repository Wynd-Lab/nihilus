<?php

namespace Nihilus;

interface CommandBusInteface
{
    public function execute(CommandInterface $command): void;
}
