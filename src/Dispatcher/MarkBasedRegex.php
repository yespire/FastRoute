<?php

declare(strict_types=1);

namespace FastRoute\Dispatcher;

use function preg_match;

class MarkBasedRegex extends AbstractRegexBased
{
    /**
     * {@inheritDoc}
     */
    protected function dispatchVariableRoute(array $routeData, string $uri): ResultInterface
    {
        foreach ($routeData as $data) {
            if (!preg_match($data['regex'], $uri, $matches)) {
                continue;
            }

            [$handler, $varNames, $route] = $data['routeMap'][$matches['MARK']];

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
