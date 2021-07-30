<?php

declare(strict_types=1);

use Viktorprogger\Container\Test\Stub\App\ModuleRoot3\Module3Interface;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot3\Module3InterfaceImpl1;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot3\Module3InterfaceImpl2;

return [
    'test' => ['namespace' => 'Viktorprogger\\Container\\Test\\Stub\\App'],

    'root1' => [
        'namespace' => 'Viktorprogger\\Container\\Test\\Stub\\App\\ModuleRoot1',
    ],
    'root2' => [
        'namespace' => 'Viktorprogger\\Container\\Test\\Stub\\App\\ModuleRoot2',
        'dependencies' => ['root3'],
    ],
    'root3' => [
        'namespace' => 'Viktorprogger\\Container\\Test\\Stub\\App\\ModuleRoot3',
        'definitions' => [
            Module3Interface::class => Module3InterfaceImpl2::class
        ],
    ],
    'root1/submodule1' => [
        'namespace' => 'Viktorprogger\\Container\\Test\\Stub\\App\\ModuleRoot1\\Submodule1',
        'dependencies' => ['root2', 'root3'],
    ],
    'root1/submodule2' => [
        'namespace' => 'Viktorprogger\\Container\\Test\\Stub\\App\\ModuleRoot1\\Submodule2',
    ],
    'root1/submodule1/subsubmodule' => [
        'namespace' => 'Viktorprogger\\Container\\Test\\Stub\\App\\ModuleRoot1\\Submodule1\\SubSubmodule',
        'definitions' => [
            Module3Interface::class => Module3InterfaceImpl1::class,
        ],
    ],
];
