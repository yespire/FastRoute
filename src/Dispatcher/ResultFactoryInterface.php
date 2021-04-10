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
     * @inheritDoc
     */
    public function createResult(
        int $status = ResultInterface::NOT_FOUND,
        $handler = null,
        ?RouteInterface $route = null
    ): ResultInterface;

    /**
     * @param mixed[] $result
     */
    public function createResultFromArray(array $result): ResultInterface;

    /**
     * @param array $allowedMethods
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    public function createNotAllowed(array $allowedMethods): ResultInterface;

    /**
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    public function createNotFound(): ResultInterface;
}
