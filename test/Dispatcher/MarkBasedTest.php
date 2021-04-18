<?php

declare(strict_types=1);

namespace FastRoute\Test\Dispatcher;

use FastRoute\DataGenerator\MarkBasedProcessor;
use FastRoute\DataGenerator\RegexBased;
use FastRoute\Dispatcher\MarkBasedRegex;
use FastRoute\RouteFactory;

/**
 * MarkBasedTest
 */
class MarkBasedTest extends DispatcherTest
{
    /**
     * @inheritDoc
     */
    protected function getDispatcherClass()
    {
        return MarkBasedRegex::class;
    }

    /**
     * @inheritDoc
     */
    protected function getDataGeneratorClass()
    {
        return new RegexBased(
            new MarkBasedProcessor(),
            new RouteFactory()
        );
    }
}
