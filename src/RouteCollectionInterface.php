<?php

declare(strict_types=1);

namespace FastRoute;

interface RouteCollectionInterface
{
    /**
     * Adds a route to the collection.
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethod
     * @param string $route
     * @param mixed $handler
     */
    public function addRoute($httpMethod, string $route, $handler): void;

    /**
     * Create a route group with a common prefix.
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string $prefix
     * @param callable $callback
     */
    public function addGroup(string $prefix, callable $callback): void;

    /**
     * Adds a GET route to the collection
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function get(string $route, $handler): void;

    /**
     * Adds a POST route to the collection
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function post(string $route, $handler): void;

    /**
     * Adds a PUT route to the collection
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function put(string $route, $handler): void;

    /**
     * Adds a DELETE route to the collection
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function delete(string $route, $handler): void;

    /**
     * Adds a PATCH route to the collection
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function patch(string $route, $handler): void;

    /**
     * Adds a HEAD route to the collection
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function head(string $route, $handler): void;

    /**
     * Adds an OPTIONS route to the collection
     * This is simply an alias of $this->addRoute('OPTIONS', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     */
    public function options(string $route, $handler): void;

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return mixed[]
     */
    public function getData(): array;

    /**
     * Returns the collected route data, as provided by the data generator.
     */
    public function toArray(): array;
}
