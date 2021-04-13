<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test;

use Closure;
use PHPUnit\Framework\TestCase;
use Viktorprogger\Container\RootContainer;
use Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule1\DependencyConfigured;
use Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule1\DependencyConfiguredInDependentModule;
use Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule1\SubmoduleWithoutDependencies;
use Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule1\SubSubmodule\DependencyConfiguredParent;
use Viktorprogger\Container\Test\Stub\ModuleRoot1\Submodule1\SubSubmodule\DependencyRedefined;
use Viktorprogger\Container\Test\Stub\ModuleRoot1\TopLevelWithoutDependencies;
use Viktorprogger\Container\Test\Stub\ModuleRoot3\Module3InterfaceImpl1;
use Viktorprogger\Container\Test\Stub\ModuleRoot3\Module3InterfaceImpl2;

class ContainerTest extends TestCase
{
    public function successProvider()
    {
        return [
            'TopLevelWithoutDependencies' => [TopLevelWithoutDependencies::class],
            'SubmoduleWithoutDependencies' => [SubmoduleWithoutDependencies::class],
            'DependencyConfigured' => [DependencyConfigured::class],
            'DependencyConfiguredParent' => [DependencyConfiguredParent::class],
            'DependencyConfiguredInDependentModule' => [
                DependencyConfiguredInDependentModule::class,
                fn(DependencyConfiguredInDependentModule $object) => self::assertInstanceOf(Module3InterfaceImpl2::class, $object->module3)
            ],
            'DependencyRedefined' => [
                DependencyRedefined::class,
                fn(DependencyRedefined $object) => self::assertInstanceOf(Module3InterfaceImpl1::class, $object->module3)
            ],
        ];
    }

    /**
     * @param string $className
     * @dataProvider successProvider
     */
    public function testSuccessful(string $className, ?Closure $assert = null): void
    {
        $container = new RootContainer(require __DIR__ . '/config.php');

        self::assertInstanceOf($className, $container->get($className));
    }
}
