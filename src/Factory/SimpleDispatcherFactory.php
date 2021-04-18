<?php

declare(strict_types=1);

namespace FastRoute\Factory;

use FastRoute\DataGenerator\MarkBasedProcessor;
use FastRoute\DataGenerator\RegexBased;
use FastRoute\Dispatcher\DispatcherInterface;
use FastRoute\Dispatcher\MarkBasedRegex;
use FastRoute\RouteCollection;
use FastRoute\RouteFactory;
use FastRoute\RouteParser\RouteParser;

use function assert;
use function is_string;

/**
 * SimpleDispatcherFactory
 */
class SimpleDispatcherFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public static function create(callable $routeDefinitionCallback, array $options = []): DispatcherInterface
    {
        $options += [
            'routeParser' => RouteParser::class,
            'dataGenerator' => new RegexBased(new MarkBasedProcessor(), new RouteFactory()),
            'dispatcher' => MarkBasedRegex::class,
            'routeCollector' => RouteCollection::class,
        ];

        $routeCollection = new $options['routeCollector'](
            is_string($options['routeParser']) ? new $options['routeParser']() : $options['routeParser'],
            is_string($options['dataGenerator']) ? new $options['dataGenerator']() : $options['dataGenerator']
        );

        assert($routeCollection instanceof RouteCollection);
        $routeDefinitionCallback($routeCollection);

        return new $options['dispatcher']($routeCollection->getData());
    }
}
