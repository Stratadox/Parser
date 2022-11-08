<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Closure;
use Stratadox\Parser\Helpers\Cast;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Ok;

/**
 * Map
 *
 * Applies a function to the data of a successful result.
 */
final class Map extends Parser
{
    public function __construct(
        private Parser $parser,
        private Closure $onSuccess,
    ) {}

    public static function the(Parser|string $parser, Closure $onSuccess): Parser
    {
        return new self(Cast::asParser($parser), $onSuccess);
    }

    public function parse(string $input): Result
    {
        $result = $this->parser->parse($input);

        if ($result->use()) {
            return Ok::with(($this->onSuccess)($result->data()), $result->unparsed());
        }
        return $result;
    }
}
