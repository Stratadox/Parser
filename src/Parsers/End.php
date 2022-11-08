<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;

/**
 * End
 *
 * Returns an error result if there is unparsed content remaining after parsing.
 */
final class End extends Parser
{
    public function __construct(
        private Parser $parser,
    ) {}

    public static function with(Parser $parser): Parser
    {
        return new self($parser);
    }

    public function parse(string $input): Result
    {
        $result = $this->parser->parse($input);

        if ('' === $result->unparsed()) {
            return $result;
        }
        return Error::in($result->unparsed());
    }
}
