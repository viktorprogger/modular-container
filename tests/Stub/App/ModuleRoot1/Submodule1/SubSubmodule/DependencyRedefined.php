<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule1\SubSubmodule;

use Viktorprogger\Container\Test\Stub\App\ModuleRoot3\Module3Interface;

class DependencyRedefined
{
    public Module3Interface $module3;

    public function __construct(Module3Interface $module3)
    {
        $this->module3 = $module3;
    }
}
