<?php

declare(strict_types=1);

namespace Viktorprogger\Container;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Yiisoft\Factory\Definitions\ArrayDefinition;
use Yiisoft\Factory\Definitions\Normalizer;

class ModuleContainer implements ContainerInterface
{
    private array $building = [];
    private array $resolved = [];
    private string $namespace;
    private array $definitions;
    private array $submoduleContainers = [];
    private array $submoduleDefinitions;

    public function __construct(
        string $namespace,
        array $definitions,
        array $submoduleDefinitions = []
    ) {
        $this->namespace = $namespace;
        $this->definitions = $definitions;
        $this->submoduleDefinitions = $submoduleDefinitions;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->resolved[$id] ?? $this->resolve($id);
    }

    public function has($id): bool
    {
        return $id === ContainerInterface::class
            || isset($this->resolved[$id])
            || isset($this->definitions[$id])
            || strpos($id, $this->namespace) === 0;
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    private function resolve(string $id)
    {
        if ($id === ContainerInterface::class) {
            return $this;
        }
        $this->building[$id] = true;

        if (isset($this->building[$id])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Circular reference to "%s" detected while building: %s.',
                    $id,
                    implode(',', array_keys($this->building))
                )
            );
        }

        try {
            if (isset($this->definitions[$id])) {
                $this->resolved[$id] = $this->build($this->definitions[$id]);
            } elseif (class_exists($id) && strpos($id, $this->namespace) === 0) {
                $container = $this->getSubmoduleContainer($id);
                if ($container === null) {
                    $this->resolved[$id] = $this->build($id);
                } else {
                    $this->resolved[$id] = $container->get($id);
                }
            }
        } finally {
            unset($this->building[$id]);
        }

        throw new NofFoundException();
    }

    private function build($definition)
    {
        if (is_string($definition)) {
            $definition = new ArrayDefinition($definition);
        } else {
            $definition = Normalizer::normalize($definition);
        }

        return $definition->resolve($this);
    }

    private function getSubmoduleContainer(string $id): ?ModuleContainer
    {
        $namespaceChosen = null;
        $length = 0;

        foreach ($this->submoduleDefinitions as $namespace => $definitions) {
            $match = $this->getNamespaceMatch($id, $namespace);
            if ($match > $length) {
                $length = $match;
                $namespaceChosen = $namespace;
            }
        }

        if ($namespaceChosen !== null) {
            if (!isset($this->submoduleContainers[$namespaceChosen])) {
                $definitions = $this->submoduleDefinitions[$namespaceChosen];
                $submodules = $definitions['#submodules'];
                unset($definitions['#submodules']);

                $this->submoduleContainers[$namespaceChosen] = new ModuleContainer(
                    $namespaceChosen,
                    $definitions,
                    $submodules
                );
            }

            return $this->submoduleContainers[$namespaceChosen];
        }

        return null;
    }

    private function getNamespaceMatch(string $className, string $namespace): int
    {
        $idNamespace = explode('\\', trim($className, '\\'));
        array_pop($idNamespace); // remove class name

        $namespaceDivided = explode('\\', $namespace);

        $result = 0;
        foreach ($namespaceDivided as $i => $part) {
            if ($idNamespace[$i] === $part) {
                $result++;
            } else {
                return $result;
            }
        }

        return $result;
    }
}
