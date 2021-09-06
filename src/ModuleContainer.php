<?php

namespace Viktorprogger\Container;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Yiisoft\Definitions\Infrastructure\Normalizer;

final class ModuleContainer implements ContainerInterface
{
    private array $building = [];
    private array $resolved = [];
    private DependencyResolver $dependencyResolver;

    public function __construct(
        private string $id,
        private array $definitions,
        private ContainerConfiguration $configuration,
    ) {
        $this->dependencyResolver = new DependencyResolver($this);
    }

    public function get(string $id): object
    {
        if ($id === ContainerInterface::class) {
            return $this;
        }

        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        $container = $this->configuration->getContainer($id, $this->id);
        if ($container === $this) {
            return $this->build($id);
        }

        return $container->get($id);
    }

    public function has(string $id): bool
    {
        return $id === ContainerInterface::class
            || isset($this->resolved[$id])
            || isset($this->definitions[$id]);
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
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
            $definition = $this->definitions[$id] ?? $id;
            $this->resolved[$id] = Normalizer::normalize($definition)->resolve($this->dependencyResolver);
        } finally {
            unset($this->building[$id]);
        }

        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        throw new NotFoundException();
    }
}
