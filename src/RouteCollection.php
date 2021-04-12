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
     * @var array<string, \FastRoute\RouteInterface>
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
     * Adds a route to the collection.
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethod
     * @param string $route
     * @param mixed $handler
     * @param string|null $name
     */
    public function addRoute($httpMethod, string $route, $handler, ?string $name = null): void
    {
        $route = $this->currentGroupPrefix . $route;
        $routingData = $this->routeParser->parse($route);
        foreach ((array) $httpMethod as $method) {
            foreach ($routingData as $routeData) {
                $route = $this->dataGenerator->addRoute($method, $routeData, $handler, $name);
                if ($route->name()) {
                    $this->namedRoutes[$route->name()] = $route;
                }
            }
        }
    }

    /**
     * Gets a route by name
     *
     * @param string $name
     * @return \FastRoute\RouteInterface|null
     */
    public function getRouteByName(string $name): ?RouteInterface
    {
        return $this->namedRoutes[$name] ?? null;
    }

    /**
     * Create a route group with a common prefix.
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string $prefix
     * @param callable $callback
     */
    public function addGroup(string $prefix, callable $callback): void
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $this->currentGroupPrefix = $previousGroupPrefix . $prefix;

        $callback($this);

        $this->currentGroupPrefix = $previousGroupPrefix;
    }

    /**
     * Adds a GET route to the collection
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function get(string $route, $handler): void
    {
        $this->addRoute('GET', $route, $handler);
    }

    /**
     * Adds a POST route to the collection
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function post(string $route, $handler): void
    {
        $this->addRoute('POST', $route, $handler);
    }

    /**
     * Adds a PUT route to the collection
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function put(string $route, $handler): void
    {
        $this->addRoute('PUT', $route, $handler);
    }

    /**
     * Adds a DELETE route to the collection
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function delete(string $route, $handler): void
    {
        $this->addRoute('DELETE', $route, $handler);
    }

    /**
     * Adds a PATCH route to the collection
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function patch(string $route, $handler): void
    {
        $this->addRoute('PATCH', $route, $handler);
    }

    /**
     * Adds a HEAD route to the collection
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function head(string $route, $handler): void
    {
        $this->addRoute('HEAD', $route, $handler);
    }

    /**
     * Adds an OPTIONS route to the collection
     * This is simply an alias of $this->addRoute('OPTIONS', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function options(string $route, $handler): void
    {
        $this->addRoute('OPTIONS', $route, $handler);
    }

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return mixed[]
     */
    public function getData(): array
    {
        return $this->dataGenerator->getData();
    }

    public function toArray(): array
    {
        return $this->getData();
    }
}
