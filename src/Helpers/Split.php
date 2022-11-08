<?php declare(strict_types=1);

namespace Stratadox\Parser\Helpers;

use Closure;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\Repeatable;
use Stratadox\Parser\Parsers\Either;
use Stratadox\Parser\Parsers\Map;
use Stratadox\Parser\Parsers\Sequence;
use function array_filter;
use function array_map;
use function array_reduce;
use function count;
use function is_array;

/**
 * Split
 *
 * Splits content based on a separator.
 */
final class Split
{
    public static function with(Parser|string $separator, Parser|string $subject): Parser
    {
        return self::make(
            $separator,
            $subject,
            self::defaultMap(),
            1,
        );
    }

    public static function optional(Parser|string $separator, Parser|string $subject): Parser
    {
        return self::make(
            $separator,
            $subject,
            self::defaultMap(),
            0,
        );
    }

    private static function defaultMap(): Closure
    {
        return fn(array $r) => count($r) > 1 ? [$r[0], ...array_map(
            fn($x) => $x[1],
            array_filter($r[1], fn($x) => count($x) > 1)
        )] : array_map(
            fn($x) => $x[1],
            array_filter($r[0], fn($x) => count($x) > 1)
        );
    }

    private static function make(
        Parser|string $separator,
        Parser|string $subject,
        Closure $mapping,
        int $min,
    ): Parser {
        return Map::the(Sequence::of(
            $subject,
            AtLeast::results($min, Repeatable::parser(Sequence::of($separator, $subject))),
        ), $mapping);
    }

    public static function keep(
        Parser|string|array $separator,
        Parser|string $subject,
        ?Closure $map = null
    ): Parser {
        $map = $map ?: fn($op, $left, $right) => [$op => [$left, $right]];
        return self::make(
            is_array($separator) ? Either::of(...$separator) : $separator,
            $subject,
            fn(array $a) => array_reduce(
                [$a[0], ...$a[1]],
                fn($carry, $x) => is_array($x) && isset($x[0]) ? $map($x[0], $carry, $x[1]) : $x,
            ),
            1,
        );
    }
}
