<?php
declare(strict_types=1);

namespace FastRoute;

use FastRoute\DataGenerator\MarkBasedProcessor;
use LogicException;
use RuntimeException;
use function assert;
use function file_exists;
use function file_put_contents;
use function function_exists;
use function is_array;
use function var_export;

if (! function_exists('FastRoute\simpleDispatcher')) {
    /**
     * @param array<string, string> $options
     */
    function simpleDispatcher(callable $routeDefinitionCallback, array $options = []): Dispatcher
    {
        $options += [
            'routeParser' => RouteParser\Std::class,
            'dataGenerator' => new DataGenerator\RegexBased(new MarkBasedProcessor()),
            'dispatcher' => Dispatcher\MarkBased::class,
            'routeCollector' => RouteCollection::class,
        ];

        $routeCollector = new $options['routeCollector'](
            is_string($options['routeParser']) ? new $options['routeParser']() : $options['routeParser'],
            is_string($options['dataGenerator']) ? new $options['dataGenerator']() : $options['dataGenerator']
        );
        assert($routeCollector instanceof RouteCollection);
        $routeDefinitionCallback($routeCollector);

        return new $options['dispatcher']($routeCollector->getData());
    }

    /**
     * @param array<string, string> $options
     */
    function cachedDispatcher(callable $routeDefinitionCallback, array $options = []): Dispatcher
    {
        $options += [
            'routeParser' => RouteParser\Std::class,
            'dataGenerator' => new DataGenerator\RegexBased(new MarkBasedProcessor()),
            'dispatcher' => Dispatcher\MarkBased::class,
            'routeCollector' => RouteCollection::class,
            'cacheDisabled' => false,
        ];

        if (! isset($options['cacheFile'])) {
            throw new LogicException('Must specify "cacheFile" option');
        }

        if (! $options['cacheDisabled'] && file_exists($options['cacheFile'])) {
            $dispatchData = require $options['cacheFile'];
            if (! is_array($dispatchData)) {
                throw new RuntimeException('Invalid cache file "' . $options['cacheFile'] . '"');
            }

            return new $options['dispatcher']($dispatchData);
        }

        $routeCollector = new $options['routeCollector'](
            is_string($options['routeParser']) ? new $options['routeParser']() : $options['routeParser'],
            is_string($options['dataGenerator']) ? new $options['dataGenerator']() : $options['dataGenerator']
        );

        assert($routeCollector instanceof RouteCollection);
        $routeDefinitionCallback($routeCollector);

        $dispatchData = $routeCollector->getData();
        if (! $options['cacheDisabled']) {
            file_put_contents(
                $options['cacheFile'],
                '<?php return ' . var_export($dispatchData, true) . ';'
            );
        }

        return new $options['dispatcher']($dispatchData);
    }
}
