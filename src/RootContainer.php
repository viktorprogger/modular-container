<?php

declare(strict_types=1);

namespace Viktorprogger\Container;

class RootContainer
{
    private const DEFINITIONS_KEY = '#definitions';

    /** @var array<string, ModuleContainer> */
    private array $containers = [];

    private array $definitions = [];

    /**
     * @param array $definitions
     * Keys of a definition are:
     * - dependencies
     * - definitions
     *     - module
     *     - common
     * - submodules
     */
    public function __construct(array $definitions, array $commonDefinitions = [])
    {
        foreach ($definitions as $namespace => $moduleConfig) {
            $this->build($namespace, $moduleConfig, $commonDefinitions);
        }
    }

    private function build(string $namespace, array $moduleConfig, array $commonDefinitions): void
    {
        $commonDefinitions = array_merge($commonDefinitions, $moduleConfig['definitions']['common'] ?? []);
        $definitions = array_merge($commonDefinitions, $moduleConfig['definitions']['module']);
        $this->setDefinitions($namespace, $definitions);

        foreach ($moduleConfig['submodules'] ?? [] as $subNamespace => $subDefinitions) {
            $this->build("$namespace\\$subNamespace", $subDefinitions, $commonDefinitions);
        }
    }

    private function setDefinitions(string $namespace, array $definitions): void
    {
        $definitionBag = &$this->definitions;
        foreach (explode('\\', trim($namespace, '\\')) as $part) {
            if (!isset($definitionBag[$part])) {
                $definitionBag[$part] = [];
            }

            $definitionBag = &$definitionBag[$part];
        }

        $definitionBag[self::DEFINITIONS_KEY] = $definitions;
    }
}
