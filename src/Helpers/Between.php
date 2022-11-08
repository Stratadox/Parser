<?php declare(strict_types=1);

namespace Stratadox\Parser\Helpers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Parsers\AllOrNothing;
use Stratadox\Parser\Parsers\Any;
use Stratadox\Parser\Parsers\Either;
use Stratadox\Parser\Parsers\Except;
use Stratadox\Parser\Parsers\Ignore;
use Stratadox\Parser\Parsers\Map;
use Stratadox\Parser\Parsers\Repeatable;
use Stratadox\Parser\Parsers\Sequence;

/**
 * Between
 *
 * Matches the parser's content between start and end.
 */
final class Between
{
    public static function these(
        Parser|string $from,
        Parser|string $to,
        Parser|string $search
    ): Parser {
        return Map::the(
            Sequence::of(Ignore::the($from), $search, Ignore::the($to)),
            fn($a) => $a[0]
        );
    }

    public static function escaped(
        Parser|string $from,
        Parser|string $to,
        string $escape,
        string $escape2 = null,
    ): Parser {
        return Between::these($from, $to, Join::the(Repeatable::parser(AllOrNothing::in(Either::of(
            Map::the(($escape2 ?: $escape) . $escape, fn() => $escape), // escaped escape
            Join::the(Sequence::of(Ignore::the($escape), $to)),         // escaped end
            Except::for($to, Any::symbol()),                            // anything else
        )))));
    }
}
