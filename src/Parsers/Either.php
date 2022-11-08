<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Helpers\Cast;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;
use function array_merge;
use function strlen;
use const INF;

/**
 * Either / Or
 *
 * Returns the first matching parser of the lot.
 * If none of the parsers match, returns the error of one that got furthest.
 */
final class Either extends Parser
{
    /** @param Parser[] $options */
    public function __construct(private array $options) {}

    public static function of(Parser|string ...$options): Parser
    {
        return new self(Cast::asParsers(...$options));
    }

    public function parse(string $input): Result
    {
        $error = null;
        $errorPosition = INF;
        foreach ($this->options as $parser) {
            $result = $parser->parse($input);
            if ($result->ok()) {
                return $result;
            }
            $pos = strlen($result->unparsed());
            if ($pos < $errorPosition) {
                $errorPosition = $pos;
                $error = $result;
            }
        }
        return $error ?: Error::in($input);
    }

    public function or(string|Parser ...$other): Parser
    {
        return new self(array_merge($this->options, Cast::asParsers(...$other)));
    }
}
