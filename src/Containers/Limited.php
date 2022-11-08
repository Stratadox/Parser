<?php declare(strict_types=1);

namespace Stratadox\Parser\Containers;

use Closure;
use Stratadox\Parser\LazyContainer;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\Limit;

/**
 * Limited lazy container
 *
 * Prevents infinite looping on left-recursion.
 */
final class Limited implements LazyContainer
{
    private array $cache = [];

    public function __construct(
        private LazyContainer $container
    ) {}

    public static function recursion(LazyContainer $container): LazyContainer
    {
        return new self($container);
    }

    public function offsetGet($name): Parser
    {
        if (!isset($this->cache[$name])) {
            $this->cache[$name] = new Limit($this->container[$name]);
        }
        return $this->cache[$name];
    }

    public function offsetSet($name, $parser): void
    {
        $this->container[$name] = $parser;
    }

    public function offsetExists($name): bool
    {
        return isset($this->container[$name]);
    }

    public function offsetUnset($name): void
    {
        unset($this->container[$name]);
    }

    public function factory(string $name): Closure
    {
        return $this->container->factory($name);
    }
}
