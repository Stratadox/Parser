<?php declare(strict_types=1);

namespace Stratadox\Parser\Containers;

use Stratadox\Parser\Container;
use Stratadox\Parser\LazyContainer;
use Stratadox\Parser\Parser;

/**
 * Grammar Container
 *
 * Mixes lazy and eager containers.
 */
final class Grammar implements Container
{
    public function __construct(
        private Container     $eager,
        private LazyContainer $lazy,
    ) {}

    public static function container(): self
    {
        return new self(Eager::container(), Lazy::container());
    }

    public static function with(LazyContainer $lazy): self
    {
        return new self(Eager::container(), $lazy);
    }

    public function offsetGet($name): Parser
    {
        return $this->eager[$name] ?? $this->lazy[$name];
    }

    public function offsetSet($name, $parser): void
    {
        $this->eager[$name] = $parser;
    }

    public function offsetExists($name): bool
    {
        return isset($this->eager[$name]) || isset($this->lazy[$name]);
    }

    public function offsetUnset($name): void
    {
        unset($this->eager[$name]);
    }

    public function lazy(string $name, Parser $parser): void
    {
        $this->lazy[$name] = $parser;
    }
}
