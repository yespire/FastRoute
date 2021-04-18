<?php

declare(strict_types=1);

namespace FastRoute;

use FastRoute\DataGenerator\DataGeneratorInterface;
use FastRoute\RouteParser\RouteParserInterface;

/**
 * Class RouteCollection
 */
class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var RouteParserInterface
     */
    protected RouteParserInterface $routeParser;

    /**
     * @var DataGeneratorInterface
     */
    protected DataGeneratorInterface $dataGenerator;

    /**
     * @var string
     */
    protected string $currentGroupPrefix = '';

    /**
     * @var array<string, array>
     */
    protected array $namedRoutes = [];

    /**
     * @param RouteParserInterface $routeParser
     * @param \FastRoute\DataGenerator\DataGeneratorInterface $dataGenerator
     */
    public function __construct(
        RouteParserInterface $routeParser,
        DataGeneratorInterface $dataGenerator
    ) {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function addRoute($httpMethod, string $route, $handler, ?string $name = null): void
    {
        $route = $this->currentGroupPrefix . $route;
        $routingData = $this->routeParser->parse($route);

        foreach ((array) $httpMethod as $method) {
            foreach ($routingData as $routeData) {
                $route = $this->dataGenerator->addRoute($method, $routeData, $handler, $name);
                $name = $route->name();

                if ($name !== null && isset($this->namedRoutes[$method])) {
                    $this->namedRoutes[$method][$name] = $route;
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteByName(string $name, string $method): ?RouteInterface
    {
        return $this->namedRoutes[$method][$name] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function addGroup(string $prefix, callable $callback): void
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $this->currentGroupPrefix = $previousGroupPrefix . $prefix;

        $callback($this);

        $this->currentGroupPrefix = $previousGroupPrefix;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $route, $handler): void
    {
        $this->addRoute('GET', $route, $handler);
    }

    /**
     * {@inheritDoc}
     */
    public function post(string $route, $handler): void
    {
        $this->addRoute('POST', $route, $handler);
    }

    /**
     * {@inheritDoc}
     */
    public function put(string $route, $handler): void
    {
        $this->addRoute('PUT', $route, $handler);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $route, $handler): void
    {
        $this->addRoute('DELETE', $route, $handler);
    }

    /**
     * {@inheritDoc}
     */
    public function patch(string $route, $handler): void
    {
        $this->addRoute('PATCH', $route, $handler);
    }

    /**
     * {@inheritDoc}
     */
    public function head(string $route, $handler): void
    {
        $this->addRoute('HEAD', $route, $handler);
    }

    /**
     * {@inheritDoc}
     */
    public function options(string $route, $handler): void
    {
        $this->addRoute('OPTIONS', $route, $handler);
    }

    /**
     * {@inheritDoc}
     */
    public function getData(): array
    {
        return $this->dataGenerator->getData();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->getData();
    }
}
