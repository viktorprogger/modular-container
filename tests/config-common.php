<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Viktorprogger\Container\RootContainer;

return [
    ContainerInterface::class => new RootContainer([]),
];
