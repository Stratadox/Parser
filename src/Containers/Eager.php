<?php declare(strict_types=1);

namespace Stratadox\Parser\Containers;

use RuntimeException;
use Stratadox\Parser\Container;
use Stratadox\Parser\Parser;

/**
 * Eager container
 *
 * A basic typed list of regular parsers.
 */
final class Eager implements Container
{
    /** @var Parser[] */
    private array $parsers = [];

    public static function container(): Container
    {
        return new self();
    }

    public function offsetGet($name): Parser
    {
        if (!isset($this->parsers[$name])) {
            throw new RuntimeException("Missing the parser `$name`");
        }
        return $this->parsers[$name];
    }

    public function offsetSet($name, $parser): void
    {
        $this->parsers[$name] = $parser;
    }

    public function offsetExists($name): bool
    {
        return isset($this->parsers[$name]);
    }

    public function offsetUnset($name): void
    {
        unset($this->parsers[$name]);
    }
}
