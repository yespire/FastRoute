<?php

declare(strict_types=1);

namespace FastRoute\Test\Dispatcher;

use FastRoute\DataGenerator\CharCountProcessor;
use FastRoute\DataGenerator\RegexBased;
use FastRoute\Dispatcher\CharCountBased;
use FastRoute\RouteFactory;

/**
 * CharCountBasedTest
 */
class CharCountBasedTest extends DispatcherTest
{
    /**
     * @inheritDoc
     */
    protected function getDispatcherClass()
    {
        return CharCountBased::class;
    }

    /**
     * @inheritDoc
     */
    protected function getDataGeneratorClass()
    {
        return new RegexBased(
            new CharCountProcessor(),
            new RouteFactory()
        );
    }
}
