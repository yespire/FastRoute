<?php

declare(strict_types=1);

namespace FastRoute;

interface RouteFactoryInterface
{
    /**
     * @param string $httpMethod
     * @param mixed $handler
     * @param string $regex
     * @param array<string, mixed> $variables
     * @param bool $isStatic
     * @param string|null $name
     * @return \FastRoute\RouteInterface
     */
    public function createRoute(string $httpMethod, $handler, string $regex, array $variables, bool $isStatic = false, ?string $name = null): RouteInterface;
}
