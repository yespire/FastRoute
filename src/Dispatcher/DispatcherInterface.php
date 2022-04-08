<?php

declare(strict_types=1);

namespace FastRoute\Dispatcher;

use Psr\Http\Message\ServerRequestInterface;

interface DispatcherInterface
{
    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    /**
     * @param string $httpMethod HTTP Method
     * @param string $uri URI
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    public function dispatch(string $httpMethod, string $uri): ResultInterface;

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $serverRequest
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    public function dispatchRequest(ServerRequestInterface $serverRequest): ResultInterface;
}
