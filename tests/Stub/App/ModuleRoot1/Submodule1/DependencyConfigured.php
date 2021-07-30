<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule1;

use Viktorprogger\Container\Test\Stub\App\ModuleRoot2\TopLevelWithoutDependencies2;

class DependencyConfigured
{
    public function __construct(TopLevelWithoutDependencies2 $dependency)
    {
    }
}
