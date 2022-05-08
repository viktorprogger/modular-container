<?php

namespace Viktorprogger\Container\Configuration;

final class ModuleConfigReader
{
    public function __construct(
        public string $namespace,
        public string $configDirectory
    ) {
    }
}
