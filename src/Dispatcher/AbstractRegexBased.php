<?php

declare(strict_types=1);

namespace FastRoute\Dispatcher;

use FastRoute\RouteCollectionInterface;

abstract class AbstractRegexBased implements DispatcherInterface
{
    /**
     * @var mixed[][]
     */
    protected array $staticRouteMap = [];

    /**
     * @var mixed[]
     */
    protected array $variableRouteData = [];

    /**
     * @var ResultFactoryInterface
     */
    protected ResultFactoryInterface $resultFactory;

    /**
     * @param mixed[]|\FastRoute\RouteCollectionInterface $data
     * @param \FastRoute\Dispatcher\ResultFactoryInterface|null $resultFactory
     */
    public function __construct(
        $data,
        ?ResultFactoryInterface $resultFactory = null
    ) {
        if ($data instanceof RouteCollectionInterface) {
            $data = $data->getData();
        }

        [$this->staticRouteMap, $this->variableRouteData] = $data;

        if ($resultFactory) {
            $this->resultFactory = $resultFactory;
        } elseif (isset($data['resultFactory'])) {
            $this->resultFactory = $data['resultFactory'];
        } else {
            $this->resultFactory = new ResultFactory();
        }
    }

    /**
     * @param mixed[] $routeData
     * @param string $uri
     * @return \FastRoute\Dispatcher\ResultInterface
     */
    abstract protected function dispatchVariableRoute(array $routeData, string $uri): ResultInterface;

    /**
     * @inheritDoc
     */
    public function dispatch(string $httpMethod, string $uri): ResultInterface
    {
        if (isset($this->staticRouteMap[$httpMethod][$uri])) {
            $route = $this->staticRouteMap[$httpMethod][$uri];

            return $this->resultFactory->createResultFromArray([self::FOUND, $route->handler(), [], $route]);
        }

        $varRouteData = $this->variableRouteData;
        if (isset($varRouteData[$httpMethod])) {
            $result = $this->dispatchVariableRoute($varRouteData[$httpMethod], $uri);
            if ($result[0] === self::FOUND) {
                return $result;
            }
        }

        // For HEAD requests, attempt fallback to GET
        if ($httpMethod === 'HEAD') {
            if (isset($this->staticRouteMap['GET'][$uri])) {
                $route = $this->staticRouteMap['GET'][$uri];

                return $this->resultFactory->createResultFromArray(
                    [self::FOUND, $route->handler(), [], $route]
                );
            }

            if (isset($varRouteData['GET'])) {
                $result = $this->dispatchVariableRoute($varRouteData['GET'], $uri);
                if ($result[0] === self::FOUND) {
                    return $result;
                }
            }
        }

        // If nothing else matches, try fallback routes
        if (isset($this->staticRouteMap['*'][$uri])) {
            $route = $this->staticRouteMap['*'][$uri];

            return $this->resultFactory->createResultFromArray(
                [self::FOUND, $route->handler(), [], $route]
            );
        }

        if (isset($varRouteData['*'])) {
            $result = $this->dispatchVariableRoute($varRouteData['*'], $uri);
            if ($result[0] === self::FOUND) {
                return $result;
            }
        }

        // Find allowed methods for this URI by matching against all other HTTP methods as well
        $allowedMethods = [];

        foreach ($this->staticRouteMap as $method => $uriMap) {
            if ($method === $httpMethod || ! isset($uriMap[$uri])) {
                continue;
            }

            $allowedMethods[] = $method;
        }

        foreach ($varRouteData as $method => $routeData) {
            if ($method === $httpMethod) {
                continue;
            }

            $result = $this->dispatchVariableRoute($routeData, $uri);
            if ($result[0] !== self::FOUND) {
                continue;
            }

            $allowedMethods[] = $method;
        }

        // If there are no allowed methods the route simply does not exist
        if ($allowedMethods !== []) {
            return $this->resultFactory->createNotAllowed($allowedMethods);
        }

        return $this->resultFactory->createNotFound();
    }
}
