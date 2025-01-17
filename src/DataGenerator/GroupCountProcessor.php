<?php

declare(strict_types=1);

namespace FastRoute\DataGenerator;

use function count;
use function implode;
use function max;
use function str_repeat;

/**
 * GroupCountProcessor
 */
class GroupCountProcessor implements ChunkProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function getApproxChunkSize(): int
    {
        return 10;
    }

    /**
     * {@inheritDoc}
     */
    public function processChunk(array $regexToRoutesMap): array
    {
        $routeMap = [];
        $regexes = [];
        $numGroups = 0;
        foreach ($regexToRoutesMap as $regex => $route) {
            $numVariables = count($route->variables());
            $numGroups = max($numGroups, $numVariables);

            $regexes[] = $regex . str_repeat('()', $numGroups - $numVariables);
            $routeMap[$numGroups + 1] = [$route->handler(), $route->variables(), $route];

            ++$numGroups;
        }

        $regex = '~^(?|' . implode('|', $regexes) . ')$~';

        return ['regex' => $regex, 'routeMap' => $routeMap];
    }
}
