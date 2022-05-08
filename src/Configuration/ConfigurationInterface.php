<?php

namespace Viktorprogger\Container\Configuration;

interface ConfigurationInterface
{
    public function getModule(string $id): ModuleConfiguration;

    public function getModuleList(): array;
}
