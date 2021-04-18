<?php

declare(strict_types=1);

namespace FastRoute;

/**
 * ReverseRouteInterface
 */
interface ReverseRouteInterface
{
    /**
     * @param array<string, mixed> $vars
     * @return string
     */
    public function reverse(array $vars = []): string;
}
