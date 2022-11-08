<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Closure;
use Stratadox\Parser\Helpers\Cast;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;

/**
 * FullyMap
 *
 * Applies a function to the result, regardless of whether it was successful or
 * not.
 */
final class FullyMap extends Parser
{
    public function __construct(
        private Parser $parser,
        private Closure $map,
    ) {}

    public static function the(Parser|string $parser, Closure $map): Parser
    {
        return new self(Cast::asParser($parser), $map);
    }

    public function parse(string $input): Result
    {
        return ($this->map)($this->parser->parse($input));
    }
}
