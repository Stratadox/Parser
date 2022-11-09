# Parser Combinator

[![Github Action](https://github.com/Stratadox/Parser/actions/workflows/php.yml/badge.svg)](https://github.com/Stratadox/Parser/actions/workflows/php.yml)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stratadox/Parser/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/Stratadox/Parser/?branch=main)

*Simple Yet Powerful Parsing Library.*

## What is this

A library to create custom parsers. Based on the "ancient" concept of 
[parser combinators](https://en.wikipedia.org/wiki/Parser_combinator), this 
library contains a vast variety of base parsers, decorators, combinators and helpers.

## Why use this

Parsers made with this library can be used in many ways. 
Parsing is transforming text into a usable structure. 

This can be used for various purposes, whether it be transforming json / csv / 
xml / yaml / etc. into some kind of data structure, or parsing a custom DSL or 
expression language into an abstract syntax tree.

Whether you wish to create your own file format, your own programming language, 
interpret existing file formats or languages... This library is here to help.

## How to use this

For hands on how-tos, see the [guide](docs/guide.md).

### Installation

Using composer: `composer install stratadox/parser`

### Overview 

There's 3 base parsers: `any`, `text` and `pattern`.
- [*Any*](docs/reference.md#any-symbol) matches any single character.
- [*Text*](docs/reference.md#text) matches a predefined string.
- [*Pattern*](docs/reference.md#pattern) matches a regular expression.

These can be upgraded by a fair amount of add-ons ("decorators"), which can be 
combined as needed:
- [*Repeatable*](docs/reference.md#repeatable) applies the parser any number of 
  times, yielding a list.
- [*Map*](docs/reference.md#map) modifies successful results based on a function.
- [*Full Map*](docs/reference.md#full-map) modifies all results based on a function.
- [*Ignore*](docs/reference.md#ignore) requires the thing to be there, and then 
  ignores it. (Miauw)
- [*Maybe*](docs/reference.md#maybe) does not require it, but uses it if it's there.
- [*Optional*](docs/reference.md#optional) combines the above two.
- [*Except*](docs/reference.md#except) "un-matches" if another parser succeeds.
- [*End*](docs/reference.md#end) returns an error state if there's unparsed content.
- [*All or Nothing*](docs/reference.md#all-or-nothing) fiddles with the parse error.

Parsers can be combined using these combinators:
- [*Either / Or*](docs/reference.md#either--or) returns the first matching parser 
  of the lot.
- [*Sequence / AndThen*](docs/reference.md#sequence--andthen) puts several parsers 
  one after the other.

All the above can be mixed and combined at will. 
To make life easier, there's a bunch of combinator shortcuts for "everyday tasks":
- [*Between*](docs/reference.md#between) matches the parser's content between start 
  and end.
- [*Between Escaped*](docs/reference.md#between-escaped) matches unescaped content 
  between start and end.
- [*Split*](docs/reference.md#optional-split) yields one or more results, split 
  by a delimiter.
- [*Must Split*](docs/reference.md#mandatory-split) yields two or more results, 
  split by a delimiter.
- [*Keep Split*](docs/reference.md#mandatory-split-with-separator) yields a 
  structure like `{delimiter: [left, right]}`.

There's several additional helpers, which are essentially mapping shortcuts:
- [*Join*](docs/reference.md#join) implodes the array result into a string.
- [*Non-Empty*](docs/reference.md#non-empty) refuses `empty` results.
- [*At Least*](docs/reference.md#at-least) refuses arrays with fewer than x entries.
- [*At Most*](docs/reference.md#at-most) refuses arrays with more than x entries.
- [*First*](docs/reference.md#first) transforms an array result into its first item.
- [*Item*](docs/reference.md#item) transforms an array result into its nth item.

To enable lazy parsers (and/or to provide a structure), different containers are 
available:
- [*Lazy Container*](docs/reference.md#lazy-container) manages lazy loading, 
  essential for recursive parsers.
- [*Eager Container*](docs/reference.md#eager-container) a basic typed list of 
  regular parsers.
- [*Recursion-Safe Lazy Container*](docs/reference.md#recursion-safe-lazy-container) 
  prevents infinite looping on left-recursion.
- [*Grammar Container*](docs/reference.md#grammar-container) mixes lazy and eager 
  containers.

### Example 1: CSV

For a basic "real life" example, here's a simple CSV parser:

```php
<?php
use Stratadox\Parser\Helpers\Between;
use Stratadox\Parser\Parser;
use function Stratadox\Parser\any;
use function Stratadox\Parser\pattern;

function csvParser(
    Parser|string $sep = ',',
    Parser|string $esc = '"',
): Parser {
    $newline = pattern('\r\n|\r|\n');
    return Between::escaped('"', '"', $esc)
        ->or(any()->except($newline->or($sep)->or($esc))->repeatableString())
        ->mustSplit($sep)->maybe()
        ->split($newline)
        ->end();
}
```

(For associative result mapping, see the [CSV example](tests/Examples/CSV))

### Example 2: Calculator AST

This next example parses basic arithmetic strings (e.g. `1 + -3 * 3 ^ 2`) into an 
abstract syntax tree:

```php
<?php
use Stratadox\Parser\Containers\Grammar;
use Stratadox\Parser\Containers\Lazy;
use Stratadox\Parser\Parser;
use function Stratadox\Parser\pattern;
use function Stratadox\Parser\text;

function calculationsParser(): Parser
{
    $grammar = Grammar::with($lazy = Lazy::container());

    $sign = text('+')->or('-')->maybe();
    $digits = pattern('\d+');
    $map = fn($op, $l, $r) => [
        'op' => $op,
        'arg' => [$l, $r],
    ];

    $grammar['prio 0'] = $sign->andThen($digits, '.', $digits)->join()->map(fn($x) => (float) $x)
        ->or($sign->andThen($digits)->join()->map(fn($x) => (int) $x))
        ->between(text(' ')->or("\t", "\n", "\r")->repeatable()->optional());

    $lazy['prio 1'] = $grammar['prio 0']->andThen('^', $grammar['prio 0'])->map(fn($a) => [
        'op' => '^',
        'arg' => [$a[0], $a[2]],
    ])->or($grammar['prio 0']);

    $grammar['prio 2'] = $grammar['prio 1']->keepSplit(['*', '/'], $map)->or($grammar['prio 1']);

    $grammar['prio 3'] = $grammar['prio 2']->keepSplit(['+', '-'], $map)->or($grammar['prio 2']);

    return $grammar['prio 3']->end();
}
```

(For a working example, see the [Calculator example](tests/Examples/Calculator))

### Documentation

Additional documentation is available through the [guide](docs/guide.md), the 
[reference](docs/reference.md) and/or the [tests](tests).
