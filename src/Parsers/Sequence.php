<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Helpers\Cast;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Ok;
use function array_merge;

/**
 * Sequence / andThen
 *
 * Puts several parsers one after the other.
 * Leaves ignored results out of the returned list of results.
 */
final class Sequence extends Parser
{
    /** @param Parser[] $parsers */
    public function __construct(private array $parsers) {}

    public static function of(Parser|string ...$parsers): Parser
    {
        return new self(Cast::asParsers(...$parsers));
    }

    public function parse(string $input): Result
    {
        $sequence = [];
        $unparsed = $input;
        foreach ($this->parsers as $parser) {
            $result = $parser->parse($unparsed);
            if (!$result->ok()) {
                return $result;
            }
            if ($result->use()) {
                $sequence[] = $result->data();
            }
            $unparsed = $result->unparsed();
        }
        return Ok::with($sequence, $unparsed);
    }

    public function andThen(Parser|string ...$other): Parser
    {
        return new self(array_merge($this->parsers, Cast::asParsers(...$other)));
    }
}
