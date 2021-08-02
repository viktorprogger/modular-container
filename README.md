# Modular container

This package is a DI container concept designed to work with applications divided into modules. Each module has 
a parent, a set of children and a set of dependencies (other modules).

Minimal configuration example:

```php
$configuration = [
    'moduleId' => [
        'namespace' => 'Module\\Root\\Namespace', // You must define the root namespace for the module.
    ],
]
```

You can also define other keys in a module configuration:
- `definitions` - DI container definitions. Format is the same as in [yiisoft/di](http://github.com/yiisoft/di).
- `dependencies` - Other module IDs.
- `parent` - you can manually define module's parent, it should be parent's module id. If not defined, it will be computed automatically based on PSR-4.
- `children` - you can manually define module's children modules, this parameter must be an array of module IDs. Anyway, modules will be computed automatically.
- `reset` - boolean value. `True` indicates this module can't work with long-running applications
(RoadRunner, Swoole, etc.), e.g. has stateful services, and should be reset between requests.

## Ограничения

// TODO: translate into English

- Дочерний модуль наследует зависимости и дефинишены родителя.
**Сейчас дефинишены не мержатся с родительскими, а переопределяются полностью.**
- Дочерний модуль не может использовать классы родительского, т.к. не видит код за пределами себя
- Родительский модуль может использовать классы дочернего модуля.
- Родительский модуль не может использовать зависимости дочернего модуля.

# Ближайшие планы
- Сделать мерж дефинишенов дочерних с родительскими
- Перетащить фичи из yiisoft/di (сначала - составить их список)
