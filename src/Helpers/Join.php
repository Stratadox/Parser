<?php declare(strict_types=1);

namespace Stratadox\Parser\Helpers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\Map;
use function implode;
use function is_array;

/**
 * Join
 *
 * Implodes the array result into a string.
 */
final class Join
{
    public static function the(Parser|string $parser): Parser
    {
        return Join::with('', $parser);
    }

    public static function with(string $glue, Parser|string $parser): Parser
    {
        return Map::the($parser, fn($x) => is_array($x) ? implode($glue, $x) : $x);
    }
}
