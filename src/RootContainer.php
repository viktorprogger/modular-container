<?php

namespace Viktorprogger\Container;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;

final class RootContainer implements ContainerInterface
{
    public function __construct(
        private ContainerConfiguration $configuration,
        private string $rootModule = 'application'
    ) {
        try {
            $this->configuration->getContainer(null, $rootModule);
        } catch(InvalidArgumentException $exception) {
            throw new InvalidArgumentException('Root module container does not exist', previous: $exception);
        }
    }

    public function get(string $id)
    {
        return $this->configuration->getContainer($id, $this->rootModule)->get($id);
    }

    public function has(string $id): bool
    {
        return $this->configuration->getContainer($id, $this->rootModule)->has($id);
    }
}
