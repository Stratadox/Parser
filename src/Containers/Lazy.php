<?php declare(strict_types=1);

namespace Stratadox\Parser\Containers;

use Closure;
use Stratadox\Parser\LazyContainer;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\Lazily;

/**
 * Lazy Container
 *
 * Manages lazy loading, essential for recursive parsers.
 */
final class Lazy implements LazyContainer
{
    private array $registry = [];

    public static function container(): LazyContainer
    {
        return new self();
    }

    public function offsetExists($name): bool
    {
        return isset($this->registry[$name]);
    }

    public function offsetUnset($name): void
    {
        unset($this->registry[$name]);
    }

    public function offsetSet($name, $parser): void
    {
        $this->registry[$name] = fn() => $parser;
    }

    public function offsetGet($name): Parser
    {
        return new Lazily($this, $name);
    }

    public function factory(string $name): Closure
    {
        return $this->registry[$name] ?? fn() => null;
    }
}
