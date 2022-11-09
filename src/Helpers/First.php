<?php declare(strict_types=1);

namespace Stratadox\Parser\Helpers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\Map;
use function is_array;

/**
 * First
 *
 * Transforms an array result into its first item.
 */
final class First
{
    public static function of(Parser $parser): Parser
    {
        return Map::the($parser, fn($x) => is_array($x) ? ($x[0] ?? $x) : $x);
    }
}
