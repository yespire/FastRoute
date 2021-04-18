<?php

declare(strict_types=1);

namespace FastRoute\Test\Dispatcher;

use FastRoute\DataGenerator\RegexBased;
use FastRoute\DataGenerator\GroupPosProcessor;
use FastRoute\Dispatcher;
use FastRoute\RouteFactory;

class GroupPosBasedTest extends DispatcherTest
{
    /**
     * @inheritDoc
     */
    protected function getDispatcherClass()
    {
        return Dispatcher\GroupPosBased::class;
    }

    /**
     * @inheritDoc
     */
    protected function getDataGeneratorClass()
    {
        return new RegexBased(
            new GroupPosProcessor(),
            new RouteFactory()
        );
    }
}
