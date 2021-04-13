<?php

declare(strict_types=1);

use Viktorprogger\Container\Test\Stub\ModuleRoot3\Module3Interface;
use Viktorprogger\Container\Test\Stub\ModuleRoot3\Module3InterfaceImpl1;
use Viktorprogger\Container\Test\Stub\ModuleRoot3\Module3InterfaceImpl2;

return [
    'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1' => [
        'dependencies' => [
            'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1\\Submodule1',
            'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1\\Submodule2',
        ],
    ],
    'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot2' => [
        'dependencies' => ['Viktorprogger\\Container\\Test\\Stub\\ModuleRoot3'],
    ],
    'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot3' => [
        'definitions' => [
            Module3Interface::class => Module3InterfaceImpl2::class
        ],
    ],
    'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1\\Submodule1' => [
        'dependencies' => [
            'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1',
            'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot2',
            'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1\\Submodule1\\SubSubmodule',
        ],
    ],
    'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1\\Submodule2' => [
        'dependencies' => [
            'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1',
        ],
    ],
    'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1\\Submodule1\\SubSubmodule' => [
        'definitions' => [
            Module3Interface::class => Module3InterfaceImpl1::class,
        ],
        'dependencies' => ['Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1\\Submodule1'],
    ],
];
