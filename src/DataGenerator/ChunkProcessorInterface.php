<?php
declare(strict_types=1);

namespace FastRoute\DataGenerator;

interface ChunkProcessorInterface
{
    /**
     * @param array<string, Route> $regexToRoutesMap
     *
     * @return mixed[]
     */
    public function processChunk(array $regexToRoutesMap): array;

    /**
     * @return int
     */
    public function getApproxChunkSize(): int;
}
