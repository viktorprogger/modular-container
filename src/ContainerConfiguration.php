<?php

namespace Viktorprogger\Container;

use Psr\Container\ContainerInterface;
use RuntimeException;

final class ContainerConfiguration
{
    public const VENDOR_CONTAINER_ID = 'vendor';

    private array $building = [];
    private array $containers = [];
    private array $definitions = [];
    /** @var array<string, ContainerInterface>[] */
    private array $resolved = [];

    public function __construct(array $definitions)
    {
        // TODO throw an exception if vendor defined?
        if (!isset($definitions[self::VENDOR_CONTAINER_ID])) {
            $definitions[self::VENDOR_CONTAINER_ID] = ['namespace' => ''];
        }

        foreach ($definitions as $moduleId => $definition) {
            $this->buildContainerDefinition($moduleId, $definitions);
        }
    }

    public function getContainer(?string $id, string $moduleId): ContainerInterface
    {
        if ($id === null) {
            return $this->getModuleContainer($moduleId, $moduleId);
        }

        if (isset($this->resolved[$moduleId][$id])) {
            return $this->resolved[$moduleId][$id];
        }

        if (class_exists($id) || interface_exists($id)) {
            return $this->getContainerForClass($id, $moduleId);
        }

        return $this->getContainerForId($id, $moduleId);
    }

    private function getModuleContainer(string $moduleId, string $callerId): ContainerInterface
    {
        if (!isset($this->containers[$moduleId][$callerId])) {
            if ($moduleId === $callerId) {
                $container = new ModuleContainer(
                    $moduleId,
                    $this->definitions[$moduleId]['definitions'] ?? [],
                    $this
                );
            } elseif (($definitions = $this->getDependencyDefinitions($callerId, $moduleId)) !== []) {
                $parent = $this->getModuleContainer($moduleId, $this->definitions[$moduleId]['parent'] ?? $moduleId);
                $container = (new DependencyContainer($definitions, $parent))
                    ->withResolver(new DependencyResolver($this->getModuleContainer($callerId, $callerId)));
            } else {
                $container = $this->getModuleContainer($moduleId, $this->definitions[$callerId]['parent'] ?? $moduleId);
            }

            $this->containers[$moduleId][$callerId] = $container;
        }

        return $this->containers[$moduleId][$callerId];
    }

    private function buildContainerDefinition(string $moduleId, array $definitions): void
    {
        if (isset($this->building[$moduleId])) {
            throw new RuntimeException('Circular module dependency');
        }

        if (isset($this->definitions[$moduleId])) {
            return;
        }

        $this->building[$moduleId] = true;
        $definition = $definitions[$moduleId];

        if (!isset($definition['parent'])) {
            $definition['parent'] = $this->getParent($definitions, $moduleId);
            if ($definition['parent'] !== null) {
                $this->buildContainerDefinition($definition['parent'], $definitions);
            }
        }

        $definition['children'] = $this->findChildren($definitions, $moduleId);

        $this->definitions[$moduleId] = $definition;
        $this->definitions[$moduleId]['dependencies'] = array_unique(
            array_merge(
                   $definition['dependencies'] ?? [],
                ['vendor'],
                ...$this->getParentDependencies($moduleId),
            )
        );

        unset($this->building[$moduleId]);
    }

    private function getParent(array $definitions, string $moduleIdCurrent): ?string
    {
        $moduleNamespace = trim($definitions[$moduleIdCurrent]['namespace'], '\\'); // TODO add existence check
        $parentModuleId = null;
        $parentParts = 0;

        foreach ($definitions as $moduleId => $definition) {
            $namespace = trim($definition['namespace'], '\\'); // TODO add existence check
            $parts = explode('\\', $namespace);
            $partsCount = count($parts);

            if ($moduleId !== $moduleIdCurrent && stripos($moduleNamespace, "$namespace\\") === 0) {
                if ($parentParts < $partsCount) {
                    $parentModuleId = $moduleId;
                    $parentParts = $partsCount;
                }
            }
        }

        return $parentModuleId;
    }

    private function findChildren(array $definitions, string $moduleId): array
    {
        $result = [];
        $moduleNamespace = trim($definitions[$moduleId]['namespace'], '\\'); // TODO add existence check

        foreach ($definitions as $id => $definition) {
            $namespace = trim($definition['namespace'], '\\'); // TODO add existence check
            if ($id !== $moduleId && stripos("$namespace\\", $moduleNamespace) === 0) {
                $result[] = $id;
            }
        }

        return $result;
    }

    private function getParentDependencies(string $moduleId, array $dependencies = []): array
    {
        $parent = $this->definitions[$moduleId]['parent'] ?? null;
        if ($parent !== null) {
            $parentDeps = $this->definitions[$parent]['dependencies'] ?? [];
            if ($parentDeps !== []) {
                $dependencies[] = $parentDeps;
            }

            $dependencies = $this->getParentDependencies($parent, $dependencies);
        }

        return $dependencies;
    }

    private function getDependencyDefinitions(string $moduleId, string $dependencyId): array
    {
        $result = [];

        $namespace = trim($this->definitions[$dependencyId]['namespace'], '\\') . '\\'; // TODO add existence check
        $definitions = $this->definitions[$moduleId]['definitions'] ?? [];
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

        foreach ($this->definitions as $module => $definition) {
            $namespace = trim($definition['namespace'], '\\') . '\\';
            if (
                stripos($classPrepared, $namespace) === 0
                && ($parts = explode('\\', $namespace)) > $partsFound
            ) {
                $moduleFound = $module;
                $partsFound = $parts;
            }
        }

        $moduleFound = $moduleFound ?? 'vendor';
        $submodules = array_merge(
            $this->definitions[$moduleId]['dependencies'],
            $this->definitions[$moduleId]['children'],
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
}
