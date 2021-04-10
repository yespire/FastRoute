<?php

declare(strict_types=1);

namespace FastRoute;

use FastRoute\Dispatcher\DispatcherInterface;
use FastRoute\Factory\CachedDispatcherFactory;
use FastRoute\Factory\SimpleDispatcherFactory;

use function function_exists;

if (!function_exists('FastRoute\simpleDispatcher')) {
    /**
     * @param array<string, string> $options
     */
    function simpleDispatcher(callable $routeDefinitionCallback, array $options = []): DispatcherInterface
    {
        return SimpleDispatcherFactory::create($routeDefinitionCallback, $options);
    }

    /**
     * @param array<string, string> $options
     */
    function cachedDispatcher(callable $routeDefinitionCallback, array $options = []): DispatcherInterface
    {
        return CachedDispatcherFactory::create($routeDefinitionCallback, $options);
    }
}
