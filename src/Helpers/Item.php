<?php declare(strict_types=1);

namespace Stratadox\Parser\Helpers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\Map;
use function is_array;

/**
 * Item
 *
 * Transforms an array result into its nth item.
 */
final class Item
{
    public static function number(int|string $n, Parser $parser): Parser
    {
        return Map::the($parser, fn($x) => is_array($x) ? ($x[$n] ?? $x) : $x);
    }
}
