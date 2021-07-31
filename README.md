# Modular container

### A DI container made to work with modules in PSR4-compliant projects.

Minimal configuration example:

```php
$configuration = [
    'moduleId' => [
        'namespace' => 'Module\\Root\\Namespace', // You must define the root namespace for the module.
        'dependencies' => [], // Other module IDs. You can omit this key when there are no dependencies for the module.
    ],
]
```

You can also define other keys in a module configuration:
- `definitions` - DI container definitions. Format is the same as in [yiisoft/di](http://github.com/yiisoft/di)
- `parent` - you can manually define module's parent, it should be parent's module id. If not defined, it will be computed automatically based on PSR-4.
- `children` - you can manually define module's children modules, this parameter must be an array of module IDs. Anyway, modules will be computed automatically.

## Working Principles

See tests, please.
