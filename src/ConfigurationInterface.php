<?php

namespace Viktorprogger\Container;

interface ConfigurationInterface
{
    public function getModule(string $id): ModuleConfiguration;

    public function getModuleList(): array;
}
