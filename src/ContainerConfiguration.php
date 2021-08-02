<?php

namespace Viktorprogger\Container;

use Psr\Container\ContainerInterface;

final class ContainerConfiguration
{
    /** @var ContainerInterface[][] */
    private array $containers = [];
    private array $resolved = [];

    public function __construct(private ConfigurationInterface $modules)
    {
    }

    /**
     * @param string|null $id
     * @param string $callerId
     *
     * @internal
     * @return ContainerInterface
     */
    public function getContainer(?string $id, string $callerId): ContainerInterface
    {
        if ($id === null) {
            return $this->getModuleContainer($callerId, $callerId);
        }

        if (!isset($this->resolved[$callerId][$id])) {
            if (class_exists($id) || interface_exists($id)) {
                $this->resolved[$callerId][$id] = $this->getContainerForClass($id, $callerId);
            } else {
                $this->resolved[$callerId][$id] = $this->getContainerForId($id, $callerId);
            }
        }

        return $this->resolved[$callerId][$id];
    }

    /**
     * Resets all containers marked as resetable and their dependants
     *
     * @see ModuleConfiguration::isResetable()
     */
    public function reset(): void
    {
        $toReset = [];
        $modules = array_map(
            static fn(ModuleConfiguration $module) => $module->getId(),
            array_filter(
                $this->modules->getModuleList(),
                static fn(ModuleConfiguration $module) => $module->isResetable()
            )
        );

        foreach ($modules as $moduleId) {
            $this->getModulesToReset($moduleId, $toReset);
        }

        foreach ($toReset as $moduleId) {
            unset($this->containers[$moduleId]);
        }
    }

    private function getModuleContainer(string $moduleId, string $callerId): ContainerInterface
    {
        if (!isset($this->containers[$moduleId][$callerId])) {
            if ($moduleId === $callerId) {
                $container = new ModuleContainer(
                    $moduleId,
                    $this->modules->getModule($moduleId)->getDefinitions(),
                    $this,
                );
            } elseif (($definitions = $this->getDependencyDefinitions($callerId, $moduleId)) !== []) {
                $parent = $this->getModuleContainer(
                    $moduleId,
                    $this->modules->getModule($moduleId)->getParent() ?? $moduleId,
                );
                $container = (new DependencyContainer($definitions, $parent))
                    ->withResolver(new DependencyResolver($this->getModuleContainer($callerId, $callerId)));
            } else {
                $container = $this->getModuleContainer(
                    $moduleId,
                    $this->modules->getModule($callerId)->getParent() ?? $moduleId,
                );
            }

            $this->containers[$moduleId][$callerId] = $container;
        }

        return $this->containers[$moduleId][$callerId];
    }

    private function getDependencyDefinitions(string $moduleId, string $dependencyId): array
    {
        $result = [];

        $namespace = $this->modules->getModule($dependencyId)->getNamespace() . '\\';
        $definitions = $this->modules->getModule($moduleId)->getDefinitions() ?? [];
        foreach ($definitions as $id => $definition) {
            if (class_exists($id) || interface_exists($id)) {
                if (stripos(trim($id, '\\'), $namespace) === 0) {
                    $result[$id] = $definition;
                }
            }
        }

        return $result;
    }

    /**
     * @param string $id
     * @param string $moduleId
     *
     * @return ContainerInterface
     * @throws NotFoundException
     */
    private function getContainerForClass(string $id, string $moduleId): ContainerInterface
    {
        $classPrepared = trim($id, '\\');

        $moduleFound = null;
        $partsFound = 0;

        foreach ($this->modules->getModuleList() as $module) {
            $namespace = $module->getNamespace() . '\\';
            if (
                stripos($classPrepared, $namespace) === 0
                && ($parts = explode('\\', $namespace)) > $partsFound
            ) {
                $moduleFound = $module->getId();
                $partsFound = $parts;
            }
        }

        $moduleFound = $moduleFound ?? $this->modules->getModule('vendor')->getId(); // TODO constant
        $submodules = array_merge(
            $this->modules->getModule($moduleId)->getDependencies(),
            $this->modules->getModule($moduleId)->getChildren(),
            [$moduleId]
        );

        if (in_array($moduleFound, $submodules, true)) {
            $this->resolved[$moduleId][$id] = $this->getModuleContainer($moduleFound, $moduleId);

            return $this->resolved[$moduleId][$id];
        }

        throw new NotFoundException($id);
    }

    private function getContainerForId(string $id, string $moduleId): ContainerInterface
    {
        if (isset($this->definitions[$moduleId][$id])) {
            return $this->getModuleContainer($moduleId, $moduleId);
        }

        if (isset($this->definitions[$moduleId]['parent']) && $this->definitions[$moduleId]['parent'] !== null) {
            return $this->getContainerForId($id, $this->definitions[$moduleId]['parent']);
        }

        throw new NotFoundException($id);
    }

    private function getModulesToReset(string $moduleId, array &$toReset = []): void
    {
        if (isset($this->containers[$moduleId])) {
            foreach (array_keys($this->containers[$moduleId]) as $dependant) {
                if (!isset($toReset[$dependant])) {
                    if ($dependant === $moduleId) {
                        $toReset[$dependant] = true;
                    } else {
                        $this->getModulesToReset($dependant, $toReset);
                    }
                }
            }
        }
    }
}
