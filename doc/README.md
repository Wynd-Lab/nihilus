# Nihilus

## Getting started

A **Query** corresponds to a read operation. Every queries must implements **QueryInterface**:

```php
use Nihilus\QueryInterface;

class GetUserByIdQuery implements QueryInterface
{
    /**
     * @var int
     */
    private $id;

    public function __construct(int $id)
    {
        $this->id = $id
    }

    public function getId(): int
    {
        return $this->id;
    }
}
```

> Write your queries and commands in a immutable is a good practice 😉

A **Query** can be handled with a dedicated **QueryHandler**:

```php
use Nihilus\QueryInterface;
use Nihilus\QueryHandlerInterface;

class GetUserByIdQueryHandler implements QueryHandlerInterface
{
    public function handle(QueryInterface $query): object
    {
        $userRepository = new UserRepository();
        $user = $userRepository.findById($query->getId());
        return $user;
    }
}
```

A **Command** corresponds to a write operation. Every commands must implements **CommandInterface**:

```php
use Nihilus\CommandInterface;

class ChangeUserEmailCommand implements CommandInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    public function __construct(int $id, string $email)
    {
        $this->id = $id;
        $this->email = $email
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
```

A **Command** can be handled with a dedicated **CommandHandler**:

```php
use Nihilus\CommandInterface;
use Nihilus\CommandHandlerInterface;

class ChangeUserEmailCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        $userRepository = new UserRepository();
        $user = $userRepository.findById($command->getId());
        $user->email = $command->email;
        $userRepository->save($user);
    }
}
```

Nihilus provides buses to execute queries and commands. Each bus is a mediator that knows which handler need to be used to handle a Query or a Command. CommandBus and QueryBus rely on an abstract resolver which needs to be implemented in your project infrastructure. Nihilus doesn't embbed a default resolver because the library is fully unopiniated about that point.

> Using a IoC container to implement Nihilus resolver can be a great solution

Wynd built a resolver for Symfony (more information on this [repository]())

CommandBus usage:
```php
use Nihilus\SymfonyResolver\CommandHandlerResolver;
use Nihilus\SymfonyResolver\CommandMiddlewareResolver;
use Nihilus\CommandBus;

$handlerResolver = new CommandHandlerResolver();
$middlewareResolver = new CommandMiddlewareResolver();
$commandBus = new CommandBus($handlerResolver, $middlewareResolver);

$commandBus->execute(new ChangeUserEmailCommand(1, 'spontoreau@wynd.eu'));
```

QueryBus usage:
```php
use Nihilus\SymfonyResolver\QueryHandlerResolver;
use Nihilus\SymfonyResolver\QueryMiddlewareResolver;
use Nihilus\QueryBus;

$handlerResolver = new QueryHandlerResolver();
$middlewareResolver = new QueryMiddlewareResolver();
$queryBus = new QueryBus($handlerResolver, $middlewareResolver);

$user = $queryBus->execute(new GetUserByIdQuery(1));
var_dump($user);
```

## Middleware

Nihilus allow you to build your own middleware directly inside CommandBus/QueryBus. Middlewares is a great way to execute generic behaviors during the Command/Query handling without using decorators. In the library, the MiddlewareDispatcher design is inspired by the PSR-15 middleware.

CommandMiddleware example:

```php
use Nihilus\CommandMiddlewareInterface;
use Nihilus\CommandInterface;
use Nihilus\CommandHandlerInterface;

class LoggerCommandMiddleware implements CommandMiddlewareInterface
{
    public function handle(CommandInterface $command, CommandHandlerInterface $next): void
    {
        var_dump("Command: {$command}");
        $next->handle($command);
    }
}
```

QueryMiddleware example:

```php
use Nihilus\QueryMiddlewareInterface;
use Nihilus\QueryInterface;
use Nihilus\QueryHandlerInterface;

class LoggerQueryMiddleware implements QueryMiddlewareInterface
{
    public function handle(QueryInterface $query, QueryHandlerInterface $next): object
    {
        var_dump("Query: {$query}");
        $result = $next->handle($query);
        var_dump("Result: {$result}");
    }
}
```