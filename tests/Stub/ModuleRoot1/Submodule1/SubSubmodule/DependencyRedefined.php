<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule1\SubSubmodule;

use Viktorprogger\Container\Test\Stub\ModuleRoot3\Module3Interface;

class DependencyRedefined
{
    public function __construct(Module3Interface $module3)
    {
    }
}
