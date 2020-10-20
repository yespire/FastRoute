<?php
declare(strict_types=1);

namespace FastRoute;

interface RouteInterface
{
    /**
     * Tests whether this route matches the given string.
     */
    public function matches(string $str): bool;
}
