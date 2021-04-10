<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Viktorprogger\Container\ModuleContainer;

return [
    'Viktorprogger\\Container\\Test\\Stub' => [
        'submodules' => [
            'SubModule1' => [
                'definitions' => [
                    ContainerInterface::class => new ModuleContainer(), // TODO
                ],
                'submodules' => [
                    'SubModule2' => [
                    ],
                ],
            ],
        ],
    ],
];
