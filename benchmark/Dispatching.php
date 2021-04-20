<?php
declare(strict_types=1);

namespace FastRoute\Benchmark;

use FastRoute\DataGenerator;
use FastRoute\DataGenerator\RegexBased;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\DispatcherInterface;
use FastRoute\RouteFactory;
use Generator;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\ParamProviders;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;

/**
 * @Warmup(2)
 * @Revs(1000)
 * @Iterations(5)
 * @BeforeMethods({"initializeDispatchers"})
 */
abstract class Dispatching
{
    /**
     * @var \FastRoute\Dispatcher\DispatcherInterface[]
     */
    private array $dispatchers = [];

    /**
     * @return void
     */
    public function initializeDispatchers(): void
    {
        $this->dispatchers['default'] = $this->createDispatcher();
        $this->dispatchers['char_count'] = $this->createDispatcher(
            [
                'dataGenerator' => new RegexBased(new DataGenerator\CharCountProcessor(), new RouteFactory()),
                'dispatcher' => Dispatcher\CharCountBased::class,
            ]
        );
        $this->dispatchers['group_pos'] = $this->createDispatcher(
            [
                'dataGenerator' => new RegexBased(new DataGenerator\GroupPosProcessor(), new RouteFactory()),
                'dispatcher' => Dispatcher\GroupPosBased::class,
            ]
        );
        $this->dispatchers['mark'] = $this->createDispatcher(
            [
                'dataGenerator' => new RegexBased(new DataGenerator\MarkBasedProcessor(), new RouteFactory()),
                'dispatcher' => Dispatcher\MarkBasedRegex::class,
            ]
        );
    }

    /**
     * @param array<string, (string|\FastRoute\DataGenerator\DataGeneratorInterface)> $options
     * @return \FastRoute\Dispatcher\DispatcherInterface
     */
    abstract protected function createDispatcher(array $options = []): DispatcherInterface;

    /**
     * @return Generator<string, array<string, mixed>>
     */
    abstract public function provideStaticRoutes(): iterable;

    /**
     * @return Generator<string, array<string, mixed>>
     */
    abstract public function provideDynamicRoutes(): iterable;

    /**
     * @return Generator<string, array<string, mixed>>
     */
    abstract public function provideOtherScenarios(): iterable;

    /**
     * @return Generator<string, array<string, string>>
     */
    public function provideDispatcher(): iterable
    {
        yield 'default' => ['dispatcher' => 'default'];
        yield 'char-count' => ['dispatcher' => 'char_count'];
        yield 'group-pos' => ['dispatcher' => 'group_pos'];
        yield 'mark' => ['dispatcher' => 'mark'];
    }

    /**
     * @ParamProviders({"provideDispatcher", "provideStaticRoutes"})
     *
     * @param array<string, mixed> $params
     */
    public function benchStaticRoutes(array $params): void
    {
        $this->runScenario($params);
    }

    /**
     * @ParamProviders({"provideDispatcher", "provideDynamicRoutes"})
     * @param array<mixed, mixed> $params
     */
    public function benchDynamicRoutes(array $params): void
    {
        $this->runScenario($params);
    }

    /**
     * @ParamProviders({"provideDispatcher", "provideOtherScenarios"})
     * @param array<mixed, mixed> $params
     */
    public function benchOtherRoutes(array $params): void
    {
        $this->runScenario($params);
    }

    /**
     * @param array<mixed, mixed> $params
     */
    private function runScenario(array $params): void
    {
        $dispatcher = $this->dispatchers[$params['dispatcher']];

        $dispatcher->dispatch($params['method'], $params['route'])->toArray();

        //Assert::assertSame($params['result'], $dispatcher->dispatch($params['method'], $params['route'])->toArray());
    }
}
