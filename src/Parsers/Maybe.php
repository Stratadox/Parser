<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Helpers\Cast;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Skip;

/**
 * Maybe
 *
 * Returns the result if successful, or an empty "skip this" result otherwise.
 */
final class Maybe extends Parser
{
    public function __construct(private Parser $maybe) {}

    public static function that(Parser|string $parser): Parser
    {
        return new self(Cast::asParser($parser));
    }

    public function parse(string $input): Result
    {
        $result = $this->maybe->parse($input);

        if ($result->ok()) {
            return $result;
        }
        return Skip::upTo($input);
    }
}
