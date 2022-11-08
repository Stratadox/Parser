<?php declare(strict_types=1);

namespace Stratadox\Parser\Test\Examples\Calculator;

use Closure;
use Stratadox\Parser\Parser;
use function array_map;
use function is_array;
use function is_string;
use function pow;

final class Calculator
{
    /**
     * @param Parser $parser
     * @param Closure[] $operations
     */
    public function __construct(
        private Parser $parser,
        private array $operations,
    ) {}

    public static function make(): Calculator
    {
        return new self(Calculations::parser(), [
            '+' => fn($x, $y) => $x + $y,
            '-' => fn($x, $y) => $x - $y,
            '*' => fn($x, $y) => $x * $y,
            '/' => fn($x, $y) => $x / $y,
            '^' => fn($x, $y) => pow($x, $y),
        ]);
    }

    public function solve(string $input): string
    {
        $parsed = $this->parser->parse($input);

        if (!$parsed->ok()) {
            return 'error: ' . $parsed->data();
        }

        return (string) $this->evaluate($parsed->data());
    }

    private function evaluate(mixed $value): mixed
    {
        if ($this->isOperator($value)) {
            return ($this->operations[$value['op']])(...array_map([$this, 'evaluate'], $value['arg']));
        }
        return $value;
    }

    private function isOperator(mixed $value): bool
    {
        return is_array($value)
            && isset($value['op'], $value['arg'])
            && is_string($value['op'])
            && is_array($value['arg']);
    }
}
