<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule2;

use Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule1\SubmoduleWithoutDependencies;

class NeighbourDependency
{
    /**
     * This should not work because neighbour module is out of scope of the current module
     *
     * @param SubmoduleWithoutDependencies $neighbour
     */
    public function __construct(SubmoduleWithoutDependencies $neighbour)
    {
    }
}
