<?php

declare(strict_types=1);

namespace FastRoute\Dispatcher;

use function count;
use function preg_match;

class GroupCountBased extends AbstractRegexBased
{
    /**
     * {@inheritDoc}
     */
    protected function dispatchVariableRoute(array $routeData, string $uri): ResultInterface
    {
        foreach ($routeData as $data) {
            if ((bool)preg_match($data['regex'], $uri, $matches) === false) {
                continue;
            }

            [$handler, $varNames, $route] = $data['routeMap'][count($matches)];

            $vars = [];
            $i = 0;
            foreach ($varNames as $varName) {
                $vars[$varName] = $matches[++$i];
            }

            return $this->resultFactory->createResultFromArray([self::FOUND, $handler, $vars, $route]);
        }

        return $this->resultFactory->createNotFound();
    }
}
