<?php

declare(strict_types=1);

namespace FastRoute\Factory;

use FastRoute\DataGenerator\MarkBasedProcessor;
use FastRoute\DataGenerator\RegexBased;
use FastRoute\Dispatcher\DispatcherInterface;
use FastRoute\Dispatcher\MarkBasedRegex;
use FastRoute\RouteCollection;
use FastRoute\RouteParser\RouteParser;
use LogicException;
use RuntimeException;

use function assert;
use function file_exists;
use function file_put_contents;
use function is_array;
use function is_string;
use function var_export;

class CachedDispatcherFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public static function create(callable $routeDefinitionCallback, array $options = []): DispatcherInterface
    {
        $options += [
            'routeParser' => RouteParser::class,
            'dataGenerator' => new RegexBased(new MarkBasedProcessor()),
            'dispatcher' => MarkBasedRegex::class,
            'routeCollector' => RouteCollection::class,
            'cacheDisabled' => false,
        ];

        if (! isset($options['cacheFile'])) {
            throw new LogicException('Must specify "cacheFile" option');
        }

        if (! (bool) $options['cacheDisabled'] && file_exists($options['cacheFile'])) {
            $dispatchData = require $options['cacheFile'];
            if (! is_array($dispatchData)) {
                throw new RuntimeException('Invalid cache file "' . $options['cacheFile'] . '"');
            }

            return new $options['dispatcher']($dispatchData);
        }

        $routeCollection = new $options['routeCollector'](
            is_string($options['routeParser']) ? new $options['routeParser']() : $options['routeParser'],
            is_string($options['dataGenerator']) ? new $options['dataGenerator']() : $options['dataGenerator']
        );

        assert($routeCollection instanceof RouteCollection);
        $routeDefinitionCallback($routeCollection);

        $dispatchData = $routeCollection->getData();
        if (! (bool) $options['cacheDisabled']) {
            file_put_contents(
                $options['cacheFile'],
                '<?php return ' . var_export($dispatchData, true) . ';'
            );
        }

        return new $options['dispatcher']($dispatchData);
    }
}
