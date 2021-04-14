<?php

declare(strict_types=1);

namespace FastRoute\Dispatcher;

use FastRoute\RouteInterface;

/**
 * Result Factory
 */
interface ResultFactoryInterface
{
    /**
     * @param int $status
     * @param null $handler
     * @param \FastRoute\RouteInterface|null $route
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    public function createResult(
        int $status = ResultInterface::NOT_FOUND,
        $handler = null,
        ?RouteInterface $route = null
    ): ResultInterface;

    /**
     * @param mixed[] $result
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    public function createResultFromArray(array $result): ResultInterface;

    /**
     * @param array<int, string> $allowedMethods
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    public function createNotAllowed(array $allowedMethods): ResultInterface;

    /**
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    public function createNotFound(): ResultInterface;
}
