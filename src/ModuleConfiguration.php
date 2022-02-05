<?php

namespace Viktorprogger\Container;

use InvalidArgumentException;

// TODO make it immutable: create a DTO for this class configuration, which can be mutable, while this class should be immutable
final class ModuleConfiguration
{
    private string $namespace;

    /**
     * @param string $id Module id
     * @param string $namespace Module root namespace
     * @param array $definitions Module container definitions
     * @param list<string> $dependencies Module dependencies (other module ids)
     * @param string|null $parent
     * @param list<string> $children
     * @param bool $resetable
     */
    public function __construct(
        private string $id,
        string $namespace,
        private array $definitions = [],
        private array $dependencies = [],
        private ?string $parent = null,
        private array $children = [],
        private bool $resetable = false,
    ) {
        $this->namespace = trim(trim($namespace), '\\');
    }

    public static function fromArray(array $configuration): self
    {
        $id = trim($configuration['id'] ?? '');
        if ($id === '') {
            throw new InvalidArgumentException('Module id must be a unique non-empty string');
        }

        $namespace = trim($configuration['namespace'] ?? '');

        $definitions = $configuration['definitions'] ?? [];
        if (!is_array($definitions)) {
            throw new InvalidArgumentException('Container definitions must be an array');
        }

        $dependencies = $configuration['dependencies'] ?? [];
        if (!is_array($dependencies)) {
            throw new InvalidArgumentException('Container dependencies must be an array');
        }

        $parent = $configuration['parent'] ?? null;
        if ($parent !== null && ($parent = trim($parent)) === '') {
            throw new InvalidArgumentException('Module parent must be either null or an another module id');
        }

        $children = $configuration['children'] ?? [];
        if (!is_array($children)) {
            throw new InvalidArgumentException('Module children must be an array');
        }

        return new self($id, $namespace, $definitions, array_unique($dependencies), $parent, array_unique($children));
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function isResetable(): bool
    {
        return $this->resetable;
    }

    public function setParent(?string $parent): void
    {
        $this->parent = $parent;
    }

    public function addChild(string $child): void
    {
        $this->children[] = $child;
        $this->children = array_unique($this->children);
    }

    public function addDependencies(string ...$dependencies): void
    {
        $this->dependencies = array_unique(array_merge($this->dependencies, $dependencies));
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @return string|null
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @return string[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
