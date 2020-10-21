<?php
declare(strict_types=1);

namespace FastRoute;

use ArrayAccess;
use RuntimeException;

/**
 * Result Object
 */
class Result implements ArrayAccess
{
    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    /** @var bool */
    protected $matched = false;

    /** @var IRoute */
    protected $route;

    /** @var mixed[] */
    protected $result = [];

    /** @var int */
    protected $status = self::NOT_FOUND;

    /** @var mixed */
    protected $handler;

    /** @var mixed[] */
    protected $args = [];

    /** @var string[] */
    protected $allowedMethods = [];

    /**
     * @return $this(FastRoute\Result)
     */
    public static function createFound(IRoute $route): self
    {
        $self = new self();
        $self->status = self::FOUND;
        $self->route = $route;
        $self->handler = $route->handler();

        return $self;
    }

    /**
     * @return $this(FastRoute\Result)
     */
    public static function createNotFound(): self
    {
        $self = new self();
        $self->result = [self::NOT_FOUND];
        $self->status = self::NOT_FOUND;

        return $self;
    }

    /**
     * @param string[] $allowedMethods
     *
     * @return $this(FastRoute\Result)
     */
    public static function createMethodNotAllowed(array $allowedMethods): self
    {
        $self = new self();
        $self->result = [self::METHOD_NOT_ALLOWED, $allowedMethods];
        $self->status = self::METHOD_NOT_ALLOWED;
        $self->allowedMethods = $allowedMethods;

        return $self;
    }

    /**
     * @param mixed[] $result Result
     *
     * @return $this(FastRoute\Result)
     */
    public static function fromArray(array $result): self
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
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->result[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->result[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException(
            'You can\'t mutate the state of the result'
        );
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException(
            'You can\'t mutate the state of the result'
        );
    }
}
