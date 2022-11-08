<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Helpers\Cast;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;

/**
 * Except
 *
 * Transforms a successful result into a failure if another parser also matches
 * the content.
 */
final class Except extends Parser
{
    public function __construct(
        private Parser $refuse,
        private Parser $parser,
    ) {}

    public static function for(Parser|string $refusal, Parser|string $parser): Parser
    {
        return new self(Cast::asParser($refusal), Cast::asParser($parser));
    }

    public function parse(string $input): Result
    {
        if ($this->refuse->parse($input)->ok()) {
            return Error::in($input);
        }
        return $this->parser->parse($input);
    }
}
