<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test\Stub;

use Viktorprogger\Container\Test\Stub\SubModule1\SubModule2\SubSubModule;

class SubSubmoduleDependency
{
    public function __construct(SubSubModule $dependency)
    {
    }
}
