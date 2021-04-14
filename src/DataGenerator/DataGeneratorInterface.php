<?php

declare(strict_types=1);

namespace FastRoute\DataGenerator;

use FastRoute\RouteInterface;

interface DataGeneratorInterface
{
    /**
     * Adds a route to the data generator. The route data uses the
     * same format that is returned by RouterParser::parser().
     * The handler doesn't necessarily need to be a callable, it
     * can be arbitrary data that will be returned when the route
     * matches.
     *
     * @param string $httpMethod
     * @param mixed[] $routeData
     * @param mixed $handler
     * @param string|null $name
     * @return \FastRoute\RouteInterface
     */
    public function addRoute(string $httpMethod, array $routeData, $handler, ?string $name): RouteInterface;

    /**
     * Returns dispatcher data in some unspecified format, which
     * depends on the used method of dispatch.
     *
     * @return mixed[]
     */
    public function getData(): array;
}
