<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use RuntimeException;
use Stratadox\Parser\LazyContainer;
use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;

/**
 * Lazily
 *
 * Lazy-loading proxy for the parser.
 */
final class Lazily extends Parser
{
    public function __construct(
        private LazyContainer $lazy,
        private string        $name,
    ) {}

    public function parse(string $input): Result
    {
        $parser = $this->lazy->factory($this->name)();

        if (!$parser instanceof Parser) {
            throw new RuntimeException("Missing the lazy parser `{$this->name}` for input `$input`");
        }

        return $parser->parse($input);
    }
}
