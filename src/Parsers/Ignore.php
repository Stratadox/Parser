<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Helpers\Cast;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Skip;

/**
 * Ignore
 *
 * Requires a successful parsing result, and then ignores it. (Miauw)
 */
final class Ignore extends Parser
{
    public function __construct(private Parser $ignored) {}

    public static function the(Parser|string $ignore): Parser
    {
        return new self(Cast::asParser($ignore));
    }

    public function parse(string $input): Result
    {
        $result = $this->ignored->parse($input);

        if ($result->ok()) {
            return Skip::upTo($result->unparsed());
        }
        return $result;
    }
}
