<?php

declare(strict_types=1);

namespace FastRoute;

interface ReverseRouteInterface
{
    /**
     * @param array $vars
     * @return string
     */
    public function reverse(array $vars = []): string;
}
