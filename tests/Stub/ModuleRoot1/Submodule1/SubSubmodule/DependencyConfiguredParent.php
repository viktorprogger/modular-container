<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule1\SubSubmodule;

use Viktorprogger\Container\Test\Stub\ModuleRoot2\TopLevelWithoutDependencies2;

class DependencyConfiguredParent
{
    public function __construct(TopLevelWithoutDependencies2 $dependency)
    {
    }
}
