<?php

declare(strict_types=1);

namespace FastRoute\Dispatcher;

use ArrayAccess;

/**
 * Result Interface
 */
interface ResultInterface extends ArrayAccess
{
    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    /**
     * @return mixed
     */
    public function handler();

    public function status(): int;

    /**
     * @return mixed
     */
    public function args();

    /**
     * @return bool
     */
    public function routeMatched(): bool;

    /**
     * @return bool
     */
    public function methodNotAllowed(): bool;

    /**
     * @return bool
     */
    public function routeNotFound(): bool;

    /**
     * @return array<mixed, mixed>
     */
    public function toArray(): array;
}
