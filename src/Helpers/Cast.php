<?php declare(strict_types=1);

namespace Stratadox\Parser\Helpers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\Text;
use function array_map;
use function is_string;

/**
 * Cast
 *
 * Casts Parser|string to Parser
 */
final class Cast
{
    public static function asParser(Parser|string $parser): Parser
    {
        return is_string($parser) ? Text::is($parser) : $parser;
    }

    public static function asParsers(Parser|string ...$parsers): array
    {
        return array_map(
            fn(Parser|string $parser) => Cast::asParser($parser),
            $parsers
        );
    }
}
