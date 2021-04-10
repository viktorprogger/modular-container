<?php

declare(strict_types=1);


namespace Viktorprogger\Container;

use Psr\Container\ContainerInterface;

class ModuleContainer implements ContainerInterface
{
    public function __construct(
        string $namespace,
        array $definitions,
        ?ContainerInterface $commonContainer = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if ($this->moduleContainer->has($id)) {
            return $this->moduleContainer->get($id);
        }

        foreach ($this->commonContainers->getContainers() as $container) {
            if ($container->has($id)) {
                return $container->get($id);
            }
        }

        foreach ($this->submoduleContainers->getContainers() as $container) {
            if ($container->has($id)) {
                return $container->get($id);
            }
        }

        foreach ($this->thirdPartyContainers->getContainers() as $container) {
            if ($container->has($id)) {
                return $container->get($id);
            }
        }

        throw new NofFoundException();
    }

    public function has(string $id): bool
    {
        // TODO: Implement has() method.
    }

    public function serve(string $id): bool
    {
        // TODO проверить, что либо $this->namespace входит в $id, либо $this->has($id), либо он есть у детей
    }
}
