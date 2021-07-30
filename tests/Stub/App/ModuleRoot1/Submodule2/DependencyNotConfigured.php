<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule2;

use Viktorprogger\Container\Test\Stub\App\ModuleRoot2\TopLevelWithoutDependencies2;

class DependencyNotConfigured
{
    public function __construct(TopLevelWithoutDependencies2 $dependency)
    {
    }
}
