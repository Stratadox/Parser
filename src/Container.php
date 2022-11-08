<?php declare(strict_types=1);

namespace Stratadox\Parser;

use ArrayAccess;

/**
 * Parser Container
 *
 * Type-safe associative collection of parsers.
 */
interface Container extends ArrayAccess
{
    /**
     * @param string $name
     */
    public function offsetGet($name): Parser;

    /**
     * @param string $name
     * @param Parser $parser
     */
    public function offsetSet($name, $parser): void;

    /**
     * @param string $name
     * @return bool
     */
    public function offsetExists($name): bool;

    /**
     * @param string $name
     */
    public function offsetUnset($name): void;
}
