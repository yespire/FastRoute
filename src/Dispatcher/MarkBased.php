<?php
declare(strict_types=1);

namespace FastRoute\Dispatcher;

use function preg_match;

class MarkBased extends RegexBasedAbstract
{
    /**
     * {@inheritDoc}
     */
    protected function dispatchVariableRoute(array $routeData, string $uri): ?array
    {
        foreach ($routeData as $data) {
            if (! preg_match($data['regex'], $uri, $matches)) {
                continue;
            }

            $route = $data['routeMap'][$matches['MARK']];

            $vars = [];
            $i = 0;
            foreach ($route->variables as $varName) {
                $vars[$varName] = $matches[++$i];
            }

            return [self::FOUND, $route->handler, $vars];
        }

        return null;
    }
}
