<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;
use Stratadox\Parser\Results\Ok;
use function array_slice;
use function count;
use function end;
use function preg_match;
use function sprintf;
use function str_contains;

/**
 * Pattern
 *
 * Parses the (first match of the) given regular expression.
 * Regex delimiters ("/") are not used.
 * Accepts an optional modifier as optional second parameter.
 */
final class Pattern extends Parser
{
    public function __construct(private string $pattern) {}

    public static function match(string $pattern, string $modifier = ''): Parser
    {
        return new self(sprintf(
            '/^%s([\s\S]*)/%s',
            str_contains($pattern, '(') ? $pattern : "($pattern)",
            $modifier,
        ));
    }

    public function parse(string $input): Result
    {
        if (preg_match($this->pattern, $input, $match)) {
            return Ok::with(
                count($match) > 3 ? array_slice($match, 1, -1) : $match[1],
                end($match)
            );
        }
        return Error::in($input);
    }
}
