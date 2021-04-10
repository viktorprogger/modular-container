<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test\Stub\SubModule1;

use Viktorprogger\Container\Test\Stub\SubModule1\SubModule2\SubSubModule;

class SubmoduleDependency
{
    public function __construct(SubSubModule $dependency)
    {
    }
}
