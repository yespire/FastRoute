<?php

declare(strict_types=1);

namespace FastRoute\Test;

use FastRoute\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function testReverse(): void
    {
        $route = new Route('GET', 'handler', '/articles/{id:\d+}[/{title}]', []);
        $result = $route->reverse(['id' => 123, 'title' => 'foo-bar']);

        self::assertSame('/articles/123/foo-bar', $result);
    }
}
