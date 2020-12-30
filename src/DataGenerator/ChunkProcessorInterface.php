<?php
declare(strict_types=1);

namespace FastRoute\DataGenerator;

interface ChunkProcessorInterface
{
    /**
     * @param array<string, \FastRoute\RouteInterface> $regexToRoutesMap
     *
     * @return mixed[]
     */
    public function processChunk(array $regexToRoutesMap): array;

    /**
     * @return int
     */
    public function getApproxChunkSize(): int;
}
