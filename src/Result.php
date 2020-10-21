<?php
declare(strict_types=1);

namespace FastRoute;

use ArrayAccess;

/**
 * Result Object
 */
class Result implements ArrayAccess
{
    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    /* @var bool */
    protected $matched = false;

    /* @var \FastRoute\Route */
    protected $route;

    /* @var array */
    protected $result = [];

    /* @var int */
    protected $status = self::NOT_FOUND;

    /* @var mixed */
    protected $handler;

    /* @var array */
    protected $args = [];

    /**
     * @param \FastRoute\RouteInterface $route
     * @return $this
     */
    public static function createFound(RouteInterface $route): self
    {
        $self = new self();
        $self->status = static::FOUND;
        $self->route = $route;
        $self->handler = $route->handler();

        return $self;
    }

    /**
     * @return $this
     */
    public static function createNotFound()
    {
        $self = new self();
        $self->result = [static::NOT_FOUND];
        $self->status = static::NOT_FOUND;

        return $self;
    }

    /**
     * @param array $result Result
     * @return $this
     */
    public static function fromArray(array $result): self
    {
        $self = new self();
        $self->result = $result;
        $self->status = $result[0];

        if ($result[0] === static::FOUND) {
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
        if (!isset($this->result[1])) {
            return null;
        }

        return $this->result[1];
    }

    /**
     * @return mixed
     */
    public function args()
    {
        if (!isset($this->result[2])) {
            return [];
        }

        return $this->result[2];
    }

    /**
     * @return bool
     */
    public function routeMatched(): bool
    {
        return $this->result[0] === self::FOUND;
    }

    /**
     * @return bool
     */
    public function methodNotAllowed(): bool
    {
        return $this->result[0] === self::METHOD_NOT_ALLOWED;
    }

    /**
     * @return bool
     */
    public function routeNotFound(): bool
    {
        return $this->result[0] === self::NOT_FOUND;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->result[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->result[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException(
            'You can\'t mutate the state of the result'
        );
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException(
            'You can\'t mutate the state of the result'
        );
    }
}
