<?php

declare(strict_types=1);

namespace FastRoute;

interface RouteFactoryInterface
{
    /**
     * @param string $httpMethod
     * @param $handler
     * @param string $regex
     * @param array $variables
     * @param bool $isStatic
     * @param string|null $name
     * @return \FastRoute\RouteInterface
     */
    public function createRoute(string $httpMethod, $handler, string $regex, array $variables, bool $isStatic = false, ?string $name = null);
}
