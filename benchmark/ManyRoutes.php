<?php
declare(strict_types=1);

namespace FastRoute\Benchmark;

use FastRoute\Dispatcher\DispatcherInterface;
use FastRoute\Dispatcher\ResultInterface;
use FastRoute\RouteCollection;
use function FastRoute\simpleDispatcher;

final class ManyRoutes extends Dispatching
{
    /**
     * {@inheritDoc}
     */
    protected function createDispatcher(array $options = []): DispatcherInterface
    {
        return simpleDispatcher(
            static function (RouteCollection $routes): void {
                for ($i = 0; $i < 400; ++$i) {
                    $routes->addRoute('GET', '/abc' . $i, ['name' => 'static-' . $i]);
                    $routes->addRoute('GET', '/abc{foo}/' . $i, ['name' => 'not-static-' . $i]);
                }
            },
            $options
        );
    }

    /**
     * {@inheritDoc}
     */
    public function provideStaticRoutes(): iterable
    {
        yield 'first' => [
            'method' => 'GET',
            'route' => '/abc0',
            'result' => [ResultInterface::FOUND, ['name' => 'static-0'], []],
        ];

        yield 'last' => [
            'method' => 'GET',
            'route' => '/abc399',
            'result' => [ResultInterface::FOUND, ['name' => 'static-399'], []],
        ];

        yield 'invalid-method' => [
            'method' => 'PUT',
            'route' => '/abc399',
            'result' => [ResultInterface::METHOD_NOT_ALLOWED, ['GET']],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function provideDynamicRoutes(): iterable
    {
        yield 'first' => [
            'method' => 'GET',
            'route' => '/abcbar/0',
            'result' => [ResultInterface::FOUND, ['name' => 'not-static-0'], ['foo' => 'bar']],
        ];

        yield 'last' => [
            'method' => 'GET',
            'route' => '/abcbar/399',
            'result' => [ResultInterface::FOUND, ['name' => 'not-static-399'], ['foo' => 'bar']],
        ];

        yield 'invalid-method' => [
            'method' => 'PUT',
            'route' => '/abcbar/399',
            'result' => [ResultInterface::METHOD_NOT_ALLOWED, ['GET']],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function provideOtherScenarios(): iterable
    {
        yield 'non-existent' => [
            'method' => 'GET',
            'route' => '/testing',
            'result' => [ResultInterface::NOT_FOUND],
        ];
    }
}
