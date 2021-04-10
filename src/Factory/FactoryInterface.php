<?php
declare(strict_types=1);

namespace FastRoute\Factory;

use FastRoute\Dispatcher\DispatcherInterface;

interface FactoryInterface
{
    /**
     * @param callable $routeDefinitionCallback Callback
     * @param mixed[]  $options                 Options
     */
    public static function make(callable $routeDefinitionCallback, array $options = []): DispatcherInterface;
}
