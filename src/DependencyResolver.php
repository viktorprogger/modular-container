<?php

declare(strict_types=1);

namespace Viktorprogger\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Definitions\Contract\DependencyResolverInterface;
use Yiisoft\Injector\Injector;

/**
 * @internal
 */
final class DependencyResolver implements DependencyResolverInterface
{
    private Injector $injector;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->injector = new Injector($this);
    }

    /**
     * @param string $id
     *
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     *
     * @return mixed|object
     *
     * @psalm-suppress InvalidThrow
     */
    public function get(string $id)
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    public function resolveReference(string $id)
    {
        return $this->get($id);
    }

    public function invoke(callable $callable): bool
    {
        return $this->injector->invoke($callable);
    }
}
