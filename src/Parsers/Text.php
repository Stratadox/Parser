<?php declare(strict_types=1);

namespace Stratadox\Parser\Parsers;

use Stratadox\Parser\Parser;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;
use Stratadox\Parser\Results\Ok;
use function str_starts_with;
use function strlen;
use function substr;

/**
 * Text
 *
 * Matches the predefined string, or returns an error result indicating where the input starts becoming
 * different. Multibyte safe, somehow, I think. If not, let me know.
 */
final class Text extends Parser
{
    private int $length;

    public function __construct(private string $text)
    {
        $this->length = strlen($this->text);
    }

    public static function is(string $text): Parser
    {
        return new self($text);
    }

    public function parse(string $input): Result
    {
        if (str_starts_with($input, $this->text)) {
            return Ok::with($this->text, substr($input, $this->length));
        }
        return Error::in(substr($input, strspn($input ^ $this->text, "\0")));
    }
}
