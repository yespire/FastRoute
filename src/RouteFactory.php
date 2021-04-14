<?php

declare(strict_types=1);

namespace FastRoute;

class RouteFactory implements RouteFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createRoute(string $httpMethod, $handler, string $regex, array $variables, bool $isStatic = false, ?string $name = null): RouteInterface
    {
        return new Route($httpMethod, $handler, $regex, $variables, $isStatic, $name);
    }
}
