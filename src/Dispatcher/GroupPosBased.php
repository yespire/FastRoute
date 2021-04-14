<?php

declare(strict_types=1);

namespace FastRoute\Dispatcher;

use function preg_match;

class GroupPosBased extends AbstractRegexBased
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

            // find first non-empty match
            /** @noinspection PhpStatementHasEmptyBodyInspection */
            for ($i = 1; $matches[$i] === ''; ++$i) {
            }

            [$handler, $varNames, $route] = $data['routeMap'][$i];

            $vars = [];
            foreach ($varNames as $varName) {
                $vars[$varName] = $matches[$i++];
            }

            return $this->resultFactory->createResultFromArray([self::FOUND, $handler, $vars, $route]);
        }

        return $this->resultFactory->createNotFound();
    }
}
