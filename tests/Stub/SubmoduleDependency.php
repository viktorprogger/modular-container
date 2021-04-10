<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test\Stub;

use Viktorprogger\Container\Test\Stub\SubModule1\SubModule;

class SubmoduleDependency
{
    public function __construct(SubModule $dependency)
    {
    }
}
