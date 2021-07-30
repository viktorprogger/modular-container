<?php

declare(strict_types=1);

namespace Viktorprogger\Container\Test;

use Closure;
use PHPUnit\Framework\TestCase;
use Viktorprogger\Container\ContainerConfiguration;
use Viktorprogger\Container\NotFoundException;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule1\DependencyConfigured;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule1\DependencyConfiguredInDependentModule;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule1\SubmoduleWithoutDependencies;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule1\SubSubmodule\DependencyConfiguredParent;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule1\SubSubmodule\DependencyRedefined;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule2\DependencyNotConfigured;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule2\NeighbourDependency;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot1\Submodule2\ParentDependency;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot1\TopLevelWithoutDependencies;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot1\VendorDependent;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot3\Module3InterfaceImpl1;
use Viktorprogger\Container\Test\Stub\App\ModuleRoot3\Module3InterfaceImpl2;

class ContainerNewTest extends TestCase
{
    public function successProvider(): array
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
            'VendorDependent' => [VendorDependent::class],
        ];
    }

    /**
     * @param string $className
     * @dataProvider successProvider
     */
    public function testSuccessful(string $className, ?Closure $assert = null): void
    {
        $object = (new ContainerConfiguration(require __DIR__ . '/config.php'))
            ->getContainer(null, 'test')
            ->get($className);

        self::assertInstanceOf($className, $object);
        if ($assert !== null) {
            $assert($object);
        }
    }

    public function failureProvider(): array
    {
        return [
            'DependencyNotConfigured' => [DependencyNotConfigured::class],
            'NeighbourDependency' => [NeighbourDependency::class],
            'ParentDependency' => [ParentDependency::class],
        ];
    }

    /**
     * @param string $className
     * @dataProvider failureProvider
     */
    public function testFailure(string $className): void
    {
        $this->expectException(NotFoundException::class);

        (new ContainerConfiguration(require __DIR__ . '/config.php'))
            ->getContainer(null, 'test')
            ->get($className);
    }
}
