<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Helpers\Cast;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Skip;

/**
 * Optional
 *
 * Ignores the parser, yielding a skip result whether the parser succeeds or not.
 */
final class Optional extends Parser
{
    public function __construct(private Parser $parser) {}

    public static function ignored(Parser|string $parser): Parser
    {
        return new self(Cast::asParser($parser));
    }

    public function parse(string $input): Result
    {
        return Skip::upTo($this->parser->parse($input)->unparsed());
    }
}
