<?php

declare(strict_types=1);

namespace FastRoute;

/**
 * Interface RouteInterface
 */
interface RouteInterface
{
    /**
     * Tests whether this route matches the given string.
     */
    public function matches(string $string): bool;

    /**
     * The handler for a route
     *
     * @return mixed
     */
    public function handler();

    /**
     * @return string
     */
    public function regex(): string;

    /**
     * @return mixed[]
     */
    public function variables(): array;

    /**
     * @return bool
     */
    public function isStatic(): bool;

    /**
     * @return string|null
     */
    public function name(): ?string;

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void;
}
