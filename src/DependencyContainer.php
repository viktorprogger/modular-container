<?php

namespace Viktorprogger\Container;

use InvalidArgumentException;
use Yiisoft\Definitions\Helpers\Normalizer;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
final class DependencyContainer implements ContainerInterface
{
    private array $building;
    private array $resolved = [];
    private ContainerInterface $dependencyResolver;

    public function __construct(
        private array $definitions,
        private ContainerInterface $parent,
    ) {
        $this->dependencyResolver = $this;
    }

    public function get(string $id)
    {
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        if (isset($this->definitions[$id])) {
            return $this->build($id);
        }

        return $this->parent->get($id);
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]) || $this->parent->has($id);
    }

    public function withResolver(ContainerInterface $resolver): self
    {
        $instance = clone $this;
        $instance->dependencyResolver = $resolver;

        return $instance;
    }

    private function build(string $id): object
    {
        if (isset($this->building[$id])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Circular reference to "%s" detected while building: %s.',
                    $id,
                    implode(',', array_keys($this->building))
                )
            );
        }
        $this->building[$id] = true;

        try {
            $this->resolved[$id] = Normalizer::normalize($this->definitions[$id])->resolve($this->dependencyResolver);
        } finally {
            unset($this->building[$id]);
        }

        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        throw new NotFoundException();
    }
}
