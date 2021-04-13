<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule2;

use Viktorprogger\Container\Test\Stub\ModuleRoot1\TopLevelWithoutDependencies;

class ParentDependency
{
    /**
     * This should not work because parent module is out of scope of the current module
     *
     * @param TopLevelWithoutDependencies $parent
     */
    public function __construct(TopLevelWithoutDependencies $parent)
    {
    }
}
