<?php

namespace Viktorprogger\Container;

use InvalidArgumentException;
use RuntimeException;

final class ModuleConfigurationCollection
{
    private const VENDOR_CONTAINER_ID = 'vendor';
    private array $building = [];
    /** @var array<string, ModuleConfiguration> */
    private array $modules = [];

    public function __construct(array $moduleDefinitions)
    {
        if (isset($definitions[self::VENDOR_CONTAINER_ID])) {
            throw new InvalidArgumentException('"vendor" module name is reserved');
        }

        $moduleDefinitions[self::VENDOR_CONTAINER_ID] = ['namespace' => ''];
        foreach ($moduleDefinitions as $id => $definition) {
            $definition['id'] = $id;

            $module = ModuleConfiguration::fromArray($definition);
            $this->modules[$module->getId()] = $module;
        }

        foreach ($this->modules as $module) {
            $this->buildContainerDefinition($module);
        }
    }

    public function getModule(string $id): ModuleConfiguration
    {
        return $this->modules[$id] ?? throw new InvalidArgumentException("Module with id '$id' not configured");
    }

    public function getModuleList(): array
    {
        return $this->modules;
    }

    private function buildContainerDefinition(ModuleConfiguration $module): void
    {
        if (isset($this->building[$module->getId()])) {
            throw new RuntimeException('Circular module dependency');
        }

        if (isset($this->definitions[$module->getId()])) {
            return;
        }

        $this->building[$module->getId()] = true;

        if ($module->getParent() === null) {
            $module->setParent($this->getParent($module));
        }

        if ($module->getParent() !== null) {
            if (!isset($this->modules[$module->getParent()])) {
                throw new RuntimeException(
                    "Module '{$module->getParent()}' is set as parent for module '{$module->getId()}', but does not exist in configuration"
                );
            }

            $this->buildContainerDefinition($this->modules[$module->getParent()]);
        }

        $this->findChildren($module);
        $module->addDependencies(...array_merge(['vendor'], ...$this->getParentDependencies($module)));
        unset($this->building[$module->getId()]);
    }

    private function getParent(ModuleConfiguration $module): ?string
    {
        $parentModuleId = null;
        $parentParts = 0;

        foreach ($this->modules as $module2) {
            $parts = explode('\\', $module2->getNamespace());
            $partsCount = count($parts);

            if (
                $module2 !== $module
                && $parentParts < $partsCount
                && stripos($module->getNamespace(), $module2->getNamespace() . '\\') === 0
            ) {
                $parentModuleId = $module2->getId();
                $parentParts = $partsCount;
            }
        }

        return $parentModuleId;
    }

    private function findChildren(ModuleConfiguration $module): void
    {
        foreach ($this->modules as $module2) {
            if ($module !== $module2 && stripos($module2->getNamespace() . '\\', $module->getNamespace()) === 0) {
                $module->addChild($module2->getId());
            }
        }
    }

    private function getParentDependencies(ModuleConfiguration $module, array $dependencies = []): array
    {
        if ($module->getParent() !== null) {
            $parentDeps = $this->modules[$module->getParent()]->getDependencies();
            if ($parentDeps !== []) {
                $dependencies[] = $parentDeps;
            }

            $dependencies = $this->getParentDependencies($this->modules[$module->getParent()], $dependencies);
        }

        return $dependencies;
    }
}
