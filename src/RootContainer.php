<?php

declare(strict_types=1);

namespace Viktorprogger\Container;

class RootContainer
{
    /** @var array<string, ModuleContainer> */
    private array $containers = [];
    private array $commonDefinitions;

    /**
     * @param array $definitions
     * Keys of a definition are:
     * - dependencies
     * - definitions
     * - submodules
     */
    public function __construct(array $definitions, array $commonDefinitions = [])
    {
        $this->commonDefinitions = $commonDefinitions;

        foreach ($definitions as $namespace => $moduleConfig) {
            $this->build($namespace, $moduleConfig, $commonDefinitions);
        }
    }

    private function build(string $namespace, array $definitions, array $commonDefinitions): void
    {
        $definitions = array_merge($commonDefinitions, $definitions);
        $this->containers[$namespace] = new ModuleContainer($namespace, $definitions);

        $submodules = $moduleConfig['submodules'] ?? [];
        foreach ($submodules as $subNamespace => $subDefinitions) {
            // TODO Remove module-specific definitions from $definitions by namespace
            $this->build("$namespace\\$subNamespace", $submodules, $definitions);
        }
    }
}
