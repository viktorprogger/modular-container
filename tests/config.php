<?php

declare(strict_types=1);

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
    ],
    'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1\\Submodule1' => [
        'dependencies' => [
            'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1',
            'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot2',
        ],
    ],
    'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1\\Submodule2' => [
        'dependencies' => [
            'Viktorprogger\\Container\\Test\\Stub\\ModuleRoot1',
        ],
    ],
];
