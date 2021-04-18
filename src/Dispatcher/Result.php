<?php

declare(strict_types=1);

namespace FastRoute\Dispatcher;

use FastRoute\RouteInterface;
use RuntimeException;

/**
 * Result Object
 */
class Result implements ResultInterface
{
    /**
     * @var bool
     */
    protected bool $matched = false;

    /**
     * @var RouteInterface|null
     */
    protected ?RouteInterface $route;

    /**
     * @var mixed[]
     */
    protected array $result = [];

    /**
     * @var int
     */
    protected int $status = self::NOT_FOUND;

    /**
     * @var mixed
     */
    protected $handler;

    /**
     * @var mixed[]
     */
    protected array $args = [];

    /**
     * @var string[]
     */
    protected array $allowedMethods = [];

    /**
     * @param int $status
     * @param mixed $handler
     * @param \FastRoute\RouteInterface|null $route
     */
    public function __construct(
        int $status = self::NOT_FOUND,
        $handler = null,
        ?RouteInterface $route = null
    ) {
        $this->status = $status;
        $this->handler = $handler;
        $this->route = $route;

        $this->result = [
            $status,
            $handler,
            $route
        ];
    }

    /**
     * @param string[] $allowedMethods
     * @return \FastRoute\Dispatcher\Result
     */
    public static function createMethodNotAllowed(array $allowedMethods): Result
    {
        $self = new self();
        $self->result = [self::METHOD_NOT_ALLOWED, $allowedMethods];
        $self->status = self::METHOD_NOT_ALLOWED;
        $self->allowedMethods = $allowedMethods;

        return $self;
    }

    /**
     * @param mixed[] $result Result
     * @return \FastRoute\Dispatcher\Result
     */
    public static function fromArray(array $result): Result
    {
        $self = new self();
        $self->result = $result;
        $self->status = $result[0];

        if ($result[0] === self::FOUND) {
            $self->handler = $result[1];
            $self->args = $result[2];
            $self->route = $result[3];
        }

        return $self;
    }

    /**
     * @return mixed
     */
    public function handler()
    {
        if (! isset($this->result[1])) {
            return null;
        }

        return $this->result[1];
    }

    public function status(): int
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function args()
    {
        if (! isset($this->result[2])) {
            return [];
        }

        return $this->result[2];
    }

    public function routeMatched(): bool
    {
        return $this->result[0] === self::FOUND;
    }

    public function methodNotAllowed(): bool
    {
        return $this->result[0] === self::METHOD_NOT_ALLOWED;
    }

    public function routeNotFound(): bool
    {
        return $this->result[0] === self::NOT_FOUND;
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->result[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->result[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException(
            'You can\'t mutate the state of the result'
        );
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException(
            'You can\'t mutate the state of the result'
        );
    }

    /**
     * Gets the legacy array
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'handler' => $this->handler,
            'route' => $this->route,
        ];
    }
}
