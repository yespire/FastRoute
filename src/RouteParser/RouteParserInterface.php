<?php
declare(strict_types=1);

namespace FastRoute\RouteParser;

/**
 * Parses route strings
 */
interface RouteParserInterface
{
    /**
     * @param string
     * @return array
     */
    public function parse(string $route): array;
}
