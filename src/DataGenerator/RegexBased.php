<?php

declare(strict_types=1);

namespace FastRoute\DataGenerator;

use FastRoute\Exception\BadRouteException;
use FastRoute\Route;
use FastRoute\RouteFactory;
use FastRoute\RouteFactoryInterface;
use FastRoute\RouteInterface;

use function array_chunk;
use function array_map;
use function ceil;
use function count;
use function is_string;
use function max;
use function preg_match;
use function preg_quote;
use function round;
use function sprintf;
use function strpos;

/**
 * RegexBased
 */
class RegexBased implements DataGeneratorInterface
{
    /**
     * @var mixed[][]
     */
    protected array $staticRoutes = [];

    /**
     * @var Route[][]
     */
    protected array $methodToRegexToRoutesMap = [];

    /**
     * @var ChunkProcessorInterface
     */
    protected ChunkProcessorInterface $chunkProcessor;

    /**
     * @var \FastRoute\RouteFactoryInterface
     */
    protected RouteFactoryInterface $routeFactory;

    /**
     * @param \FastRoute\DataGenerator\ChunkProcessorInterface $chunkProcessor
     * @param \FastRoute\RouteFactoryInterface $routeFactory
     */
    public function __construct(
        ChunkProcessorInterface $chunkProcessor,
        RouteFactoryInterface $routeFactory
    ) {
        $this->chunkProcessor = $chunkProcessor;
        $this->routeFactory = $routeFactory;
    }

    /**
     * @return int
     */
    protected function getApproxChunkSize(): int
    {
        return $this->getChunkProcessor()->getApproxChunkSize();
    }

    /**
     * @return \FastRoute\DataGenerator\ChunkProcessorInterface
     */
    protected function getChunkProcessor(): ChunkProcessorInterface
    {
        return $this->chunkProcessor;
    }

    /**
     * @param array<string, RouteInterface> $regexToRoutesMap
     *
     * @return mixed[]
     */
    protected function processChunk(array $regexToRoutesMap): array
    {
        return $this->getChunkProcessor()->processChunk($regexToRoutesMap);
    }

    /**
     * {@inheritDoc}
     */
    public function addRoute(string $httpMethod, array $routeData, $handler, ?string $name): RouteInterface
    {
        if ($this->isStaticRoute($routeData)) {
            return $this->addStaticRoute($httpMethod, $routeData, $handler);
        }

        return $this->addVariableRoute($httpMethod, $routeData, $handler);
    }

    /**
     * {@inheritDoc}
     */
    public function getData(): array
    {
        if ($this->methodToRegexToRoutesMap === []) {
            return [$this->staticRoutes, []];
        }

        return [$this->staticRoutes, $this->generateVariableRouteData()];
    }

    /**
     * @return mixed[]
     */
    protected function generateVariableRouteData(): array
    {
        $data = [];
        foreach ($this->methodToRegexToRoutesMap as $method => $regexToRoutesMap) {
            $chunkSize = $this->computeChunkSize(count($regexToRoutesMap));
            $chunks = array_chunk($regexToRoutesMap, $chunkSize, true);
            $data[$method] = array_map([$this, 'processChunk'], $chunks);
        }

        return $data;
    }

    /**
     * @param int $count
     * @return int
     */
    protected function computeChunkSize(int $count): int
    {
        $numParts = max(1, round($count / $this->getApproxChunkSize()));

        return (int) ceil($count / $numParts);
    }

    /**
     * @param array<int, mixed> $routeData
     * @return bool
     */
    protected function isStaticRoute(array $routeData): bool
    {
        return count($routeData) === 1 && is_string($routeData[0]);
    }

    /**
     * @param string $httpMethod
     * @param array<int, mixed> $routeData
     * @param mixed $handler
     * @throws \FastRoute\Exception\BadRouteException
     */
    protected function addStaticRoute(string $httpMethod, array $routeData, $handler): RouteInterface
    {
        $routeStr = $routeData[0];

        if (isset($this->staticRoutes[$httpMethod][$routeStr])) {
            throw new BadRouteException(sprintf(
                'Cannot register two routes matching "%s" for method "%s"',
                $routeStr,
                $httpMethod
            ));
        }

        if (isset($this->methodToRegexToRoutesMap[$httpMethod])) {
            foreach ($this->methodToRegexToRoutesMap[$httpMethod] as $route) {
                if ($route->matches($routeStr)) {
                    throw new BadRouteException(sprintf(
                        'Static route "%s" is shadowed by previously defined variable route "%s" for method "%s"',
                        $routeStr,
                        $route->regex(),
                        $httpMethod
                    ));
                }
            }
        }

        $this->staticRoutes[$httpMethod][$routeStr] = $this->routeFactory->createRoute(
            $httpMethod,
            $handler,
            $routeStr,
            [],
            true
        );

        return $this->staticRoutes[$httpMethod][$routeStr];
    }

    /**
     * @param string $httpMethod
     * @param array<int, mixed> $routeData
     * @param mixed $handler
     * @return \FastRoute\RouteInterface
     * @throws \FastRoute\Exception\BadRouteException
     */
    protected function addVariableRoute(string $httpMethod, array $routeData, $handler): RouteInterface
    {
        [$regex, $variables] = $this->buildRegexForRoute($routeData);

        if (isset($this->methodToRegexToRoutesMap[$httpMethod][$regex])) {
            throw new BadRouteException(sprintf(
                'Cannot register two routes matching "%s" for method "%s"',
                $regex,
                $httpMethod
            ));
        }

        $this->methodToRegexToRoutesMap[$httpMethod][$regex] = $this->routeFactory->createRoute(
            $httpMethod,
            $handler,
            $regex,
            $variables
        );

        return $this->methodToRegexToRoutesMap[$httpMethod][$regex];
    }

    /**
     * @param mixed[] $routeData
     * @return mixed[]
     * @throws \FastRoute\Exception\BadRouteException
     */
    protected function buildRegexForRoute(array $routeData): array
    {
        $regex = '';
        $variables = [];
        foreach ($routeData as $part) {
            if (is_string($part)) {
                $regex .= preg_quote($part, '~');
                continue;
            }

            [$varName, $regexPart] = $part;

            if (isset($variables[$varName])) {
                throw new BadRouteException(sprintf(
                    'Cannot use the same placeholder "%s" twice',
                    $varName
                ));
            }

            if ($this->regexHasCapturingGroups($regexPart)) {
                throw new BadRouteException(sprintf(
                    'Regex "%s" for parameter "%s" contains a capturing group',
                    $regexPart,
                    $varName
                ));
            }

            $variables[$varName] = $varName;
            $regex .= '(' . $regexPart . ')';
        }

        return [$regex, $variables];
    }

    /**
     * @param string $regex
     * @return bool
     */
    protected function regexHasCapturingGroups(string $regex): bool
    {
        if (strpos($regex, '(') === false) {
            // Needs to have at least a ( to contain a capturing group
            return false;
        }

        // Semi-accurate detection for capturing groups
        return (bool) preg_match(
            '~
                (?:
                    \(\?\(
                  | \[ [^\]\\\\]* (?: \\\\ . [^\]\\\\]* )* \]
                  | \\\\ .
                ) (*SKIP)(*FAIL) |
                \(
                (?!
                    \? (?! <(?![!=]) | P< | \' )
                  | \*
                )
            ~x',
            $regex
        );
    }
}
