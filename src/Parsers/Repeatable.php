<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Helpers\Cast;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Ok;

/**
 * Repeatable
 *
 * Makes a given parser repeatable, by parsing zero or more of its occurrences and
 * yielding a list of the results.
 */
final class Repeatable extends Parser
{
    public function __construct(private Parser $parser) {}

    public static function parser(Parser|string $parser): Parser
    {
        return new self(Cast::asParser($parser));
    }

    public function parse(string $input): Result
    {
        $results = [];
        $unparsed = $input;
        while (true) {
            $result = $this->parser->parse($unparsed);
            $unparsed = $result->unparsed();

            if ($result->use()) {
                $results[] = $result->data();
            }

            if (!$result->ok()) {
                return Ok::with($results, $unparsed);
            }
        }
    }
}
