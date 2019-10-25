<?php

use Nihilus\CommandBus;
use Nihilus\CommandHandlerInterface;
use Nihilus\CommandHandlerResolverInterface;
use Nihilus\CommandInterface;
use Nihilus\CommandMiddlewareInterface;
use Nihilus\CommandMiddlewareResolverInterface;

require './vendor/autoload.php';

/////////////////////////////////////////////////////////////
// Execute                                                 //
/////////////////////////////////////////////////////////////
var_dump('Execute');
class TestExecuteCommand implements CommandInterface
{
    /**
     * @var string
     */
    public $test;
}

class TestExecuteHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        var_dump('Handle');
    }
}

class TestExecuteMiddleware1 implements CommandMiddlewareInterface
{
    public function handle(CommandInterface $command, CommandHandlerInterface $next): void
    {
        var_dump('No handling my lord, this is a breaking middleware o_O');
    }
}

class TestExecuteMiddleware2 implements CommandMiddlewareInterface
{
    public function handle(CommandInterface $command, CommandHandlerInterface $next): void
    {
        var_dump('Before 2');
        $next->handle($command);
        var_dump('After 2');
    }
}

class TestExecuteMiddleware3 implements CommandMiddlewareInterface
{
    public function handle(CommandInterface $command, CommandHandlerInterface $next): void
    {
        var_dump('Before 3');
        $next->handle($command);
        var_dump('After 3');
    }
}

class ExecuteHandlerResolver implements CommandHandlerResolverInterface
{
    public function get(CommandInterface $command): CommandHandlerInterface
    {
        return new TestExecuteHandler();
    }

    public function getAll(CommandInterface $command): array
    {
        return [];
    }
}

class ExecuteMiddlewareResolver implements CommandMiddlewareResolverInterface
{
    public function get(CommandInterface $command): array
    {
        return [new TestExecuteMiddleware1(), new TestExecuteMiddleware2(), new TestExecuteMiddleware3()];
    }
}

$commandBus = new CommandBus(new ExecuteHandlerResolver(), new ExecuteMiddlewareResolver());
$commandBus->execute(new TestExecuteCommand());

/////////////////////////////////////////////////////////////
// Publish                                                 //
/////////////////////////////////////////////////////////////
var_dump('Publish');
class TestPublishCommand implements CommandInterface
{
    /**
     * @var string
     */
    public $test;
}

class TestPublishHandler1 implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        var_dump('Publish 1');
    }
}

class TestPublishHandler2 implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        var_dump('Publish 2');
    }
}

class TestPublishMiddleware1 implements CommandMiddlewareInterface
{
    public function handle(CommandInterface $command, CommandHandlerInterface $next): void
    {
        var_dump('Before 1');
        $next->handle($command);
        var_dump('Before 1');
    }
}

class TestPublishMiddleware2 implements CommandMiddlewareInterface
{
    public function handle(CommandInterface $command, CommandHandlerInterface $next): void
    {
        var_dump('Before 2');
        $next->handle($command);
        var_dump('After 2');
    }
}

class TestPublishMiddleware3 implements CommandMiddlewareInterface
{
    public function handle(CommandInterface $command, CommandHandlerInterface $next): void
    {
        var_dump('Before 3');
        $next->handle($command);
        var_dump('After 3');
    }
}

class PublishHandlerResolver implements CommandHandlerResolverInterface
{
    public function get(CommandInterface $command): CommandHandlerInterface
    {
        return new TestExecuteHandler();
    }

    public function getAll(CommandInterface $command): array
    {
        return [new TestPublishHandler1(), new TestPublishHandler2()];
    }
}

class PublishMiddlewareResolver implements CommandMiddlewareResolverInterface
{
    public function get(CommandInterface $command): array
    {
        return [new TestPublishMiddleware1(), new TestPublishMiddleware2(), new TestPublishMiddleware3()];
    }
}

$commandBus = new CommandBus(new PublishHandlerResolver(), new PublishMiddlewareResolver());
$commandBus->publish(new TestExecuteCommand());
