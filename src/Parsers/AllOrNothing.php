<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Helpers\Cast;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;

/**
 * All-or-Nothing
 *
 * Matches the entire result or nothing at all.
 * Used to control where the error occurs. No effect on successful results.
 */
final class AllOrNothing extends Parser
{
    public function __construct(private Parser $parser) {}

    public static function in(Parser|string $parser): Parser
    {
        return new self(Cast::asParser($parser));
    }

    public function parse(string $input): Result
    {
        $result = $this->parser->parse($input);

        if ($result->ok()) {
            return $result;
        }

        return Error::in($input);
    }
}
