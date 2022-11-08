<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;
use Stratadox\Parser\Results\Ok;
use function mb_substr;

/**
 * Any Symbol
 *
 * Matches any single character or symbol.
 * Multibyte-safe, fails on empty input.
 */
final class Any extends Parser
{
    public static function symbol(): Parser
    {
        return new self();
    }

    public function parse(string $input): Result
    {
        if ('' === $input) {
            return Error::in('');
        }
        return Ok::with(mb_substr($input, 0, 1), mb_substr($input, 1));
    }
}
