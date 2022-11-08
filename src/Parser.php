<?php declare(strict_types=1);

namespace Stratadox\Parser;

use Closure;
use Stratadox\Parser\Helpers\AtLeast;
use Stratadox\Parser\Helpers\AtMost;
use Stratadox\Parser\Helpers\Between;
use Stratadox\Parser\Helpers\First;
use Stratadox\Parser\Helpers\Item;
use Stratadox\Parser\Helpers\Join;
use Stratadox\Parser\Helpers\NonEmpty;
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\End;
use Stratadox\Parser\Parsers\Except;
use Stratadox\Parser\Parsers\Repeatable;
use Stratadox\Parser\Parsers\Either;
use Stratadox\Parser\Parsers\FullyMap;
use Stratadox\Parser\Parsers\Ignore;
use Stratadox\Parser\Parsers\Map;
use Stratadox\Parser\Parsers\Maybe;
use Stratadox\Parser\Parsers\Optional;
use Stratadox\Parser\Parsers\Sequence;

/**
 * Abstract Parser
 *
 * Provides shortcut methods for easy parser combining.
 */
abstract class Parser
{
    abstract public function parse(string $input): Result;

    public function map(Closure $map): Parser
    {
        return Map::the($this, $map);
    }

    public function fullMap(Closure $map): Parser
    {
        return FullyMap::the($this, $map);
    }

    public function nonEmpty(): Parser
    {
        return NonEmpty::result($this);
    }

    public function maybe(): Parser
    {
        return Maybe::that($this);
    }

    public function ignore(): Parser
    {
        return Ignore::the($this);
    }

    public function optional(): Parser
    {
        return Optional::ignored($this);
    }

    public function except(Parser|string $refusal): Parser
    {
        return Except::for($refusal, $this);
    }

    public function join(string $glue = ''): Parser
    {
        return Join::with($glue, $this);
    }

    public function first(): Parser
    {
        return First::of($this);
    }

    public function item(int|string $n): Parser
    {
        return Item::number($n, $this);
    }

    public function between(Parser|string $from, Parser|string $to = null): Parser
    {
        return Between::these($from, $to ?: $from, $this);
    }

    public function split(Parser|string $separator): Parser
    {
        return Split::optional($separator, $this);
    }

    public function mustSplit(Parser|string $separator): Parser
    {
        return Split::with($separator, $this);
    }

    public function keepSplit(Parser|string|array $separator, ?Closure $map = null): Parser
    {
        return Split::keep($separator, $this, $map);
    }

    public function repeatable(): Parser
    {
        return Repeatable::parser($this);
    }

    public function repeatableString(): Parser
    {
        return Join::the(Repeatable::parser($this));
    }

    public function or(Parser|string ...$other): Parser
    {
        return Either::of($this, ...$other);
    }

    public function andThen(Parser|string ...$other): Parser
    {
        return Sequence::of($this, ...$other);
    }

    public function atLeast(int $n): Parser
    {
        return AtLeast::results($n, $this);
    }

    public function atMost(int $n): Parser
    {
        return AtMost::results($n, $this);
    }

    public function end(): Parser
    {
        return End::with($this);
    }
}
