<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

class ContainerCollection
{
    private array $container;

    public function __construct(ContainerInterface  ...$container)
    {
        $this->container = $container;
    }

    public function getContainers(): array
    {
        return $this->container;
    }
}
