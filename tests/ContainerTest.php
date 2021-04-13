<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test;

use PHPUnit\Framework\TestCase;
use Viktorprogger\Container\RootContainer;
use Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule1\DependencyConfigured;
use Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule1\SubmoduleWithoutDependencies;
use Viktorprogger\Container\Test\Stub\ModuleRoot1\TopLevelWithoutDependencies;

class ContainerTest extends TestCase
{
    public function successProvider()
    {
        return [
            'TopLevelWithoutDependencies' => [TopLevelWithoutDependencies::class],
            'SubmoduleWithoutDependencies' => [SubmoduleWithoutDependencies::class],
            'DependencyConfigured' => [DependencyConfigured::class],
        ];
    }

    /**
     * @param string $className
     * @dataProvider successProvider
     */
    public function testSuccessful(string $className): void
    {
        $container = new RootContainer(require __DIR__ . '/config.php');

        self::assertInstanceOf($className, $container->get($className));
    }
}
