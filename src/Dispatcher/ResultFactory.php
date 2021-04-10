<?php

declare(strict_types=1);

namespace FastRoute\Dispatcher;

/**
 * Result Factory
 */
class ResultFactory implements ResultFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createResult(): ResultInterface
    {
        return new Result();
    }

    /**
     * @inheritDoc
     */
    public function createNotFound(): ResultInterface
    {
        return new Result(ResultInterface::NOT_FOUND);
    }

    /**
     * @inheritDoc
     */
    public function createNotAllowed(array $allowedMethods): ResultInterface
    {
        return Result::createMethodNotAllowed($allowedMethods);
    }

    /**
     * @inheritDoc
     */
    public function createResultFromArray(array $result): ResultInterface
    {
        return Result::fromArray($result);
    }
}
