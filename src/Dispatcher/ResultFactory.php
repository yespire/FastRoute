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
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    public function createNotFound(): ResultInterface
    {
        return new Result(ResultInterface::NOT_FOUND);
    }

    /**
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    public function createNotAllowed(): ResultInterface
    {
        return new Result(ResultInterface::METHOD_NOT_ALLOWED);
    }

    /**
     * @inheritDoc
     */
    public function createResultFromArray(array $result): ResultInterface
    {
        return Result::fromArray($result);
    }
}
