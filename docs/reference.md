# Parser Combinator Reference

Reference for the parser combinator library.

## Base Parsers

These are the foundation of the whole thing. 
Fairly boring in terms of capabilities, but essential for everything to work.
The core building blocks, if you will.

### Any Symbol

Parses any single character or symbol.
Multibyte-safe, fails on empty input.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Any;

$parser = Any::symbol();
```
Using function:

```php
use function Stratadox\Parser\any;

$parser = any();
```

#### Examples

Parsing a letter

```php
use Stratadox\Parser\Parsers\Any;

$parser = Any::symbol();
$result = $parser->parse('a');

assert(true === $result->ok());
assert('a' === $result->data());
assert('' === $result->unparsed());
```

Parsing only a letter

```php
use Stratadox\Parser\Parsers\Any;

$parser = Any::symbol();
$result = $parser->parse('abc');

assert(true === $result->ok());
assert('a' === $result->data());
assert('bc' === $result->unparsed());
```

Failing on empty content

```php
use Stratadox\Parser\Parsers\Any;

$parser = Any::symbol();
$result = $parser->parse('');

assert(false === $result->ok());
assert('unexpected end' === $result->data());
assert('' === $result->unparsed());
```

### Text

Parses the predefined string, or returns an error result indicating where the 
input starts becoming different. Multibyte safe. (Somehow, I think. If not, let 
me know.)

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Text;

$parser = Text::is('aa');
```

Using function:

```php
use function Stratadox\Parser\text;

$parser = text('aa');
```

#### Examples

Successful full parse:

```php
use Stratadox\Parser\Parsers\Text;

$parser = Text::is('abc');
$result = $parser->parse('abc');

assert(true === $result->ok());
assert('abc' === $result->data());
assert('' === $result->unparsed());
```

Successful partial parse:

```php
use Stratadox\Parser\Parsers\Text;

$parser = Text::is('abc');
$result = $parser->parse('abcdef');

assert(true === $result->ok());
assert('abc' === $result->data());
assert('def' === $result->unparsed());
```

Full failure:

```php
use Stratadox\Parser\Parsers\Text;

$parser = Text::is('abc');
$result = $parser->parse('foo');

assert(false === $result->ok());
assert('unexpected f' === $result->data());
assert('foo' === $result->unparsed());
```

Partial failure:

```php
use Stratadox\Parser\Parsers\Text;

$parser = Text::is('abc');
$result = $parser->parse('abd');

assert(false === $result->ok());
assert('unexpected d' === $result->data());
assert('d' === $result->unparsed());
```

### Pattern

Parses the (first match of the) given regular expression.
Regex delimiters ("/") are not used.
Accepts an optional modifier as optional second parameter.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Pattern;

$parser = Pattern::match('\d+');
```

Using named constructor with modifier:

```php
use Stratadox\Parser\Parsers\Pattern;

$parser = Pattern::match('[a-z]', 'i');
```

Using function:

```php
use function Stratadox\Parser\pattern;

$parser = pattern('\d+');
```

Using function with modifier:

```php
use function Stratadox\Parser\pattern;

$parser = pattern('[a-z]', 'i');
```

#### Examples

Successful parse:

```php
use Stratadox\Parser\Parsers\Pattern;

$parser = Pattern::match('\d+');
$result = $parser->parse('42');

assert(true === $result->ok());
assert('42' === $result->data());
assert('' === $result->unparsed());
```

With capturing group:

```php
use Stratadox\Parser\Parsers\Pattern;

$parser = Pattern::match('foo(\d)');
$result = $parser->parse('foo4abc');

assert(true === $result->ok());
assert('4' === $result->data());
assert('abc' === $result->unparsed());
```

With multiple capturing groups:

```php
use Stratadox\Parser\Parsers\Pattern;

$parser = Pattern::match('foo(\d)(\d)');
$result = $parser->parse('foo42abc');

assert(true === $result->ok());
assert(['4', '2'] === $result->data());
assert('abc' === $result->unparsed());
```

## Parser decorators

Base parsers and non-base parsers alike can be upgraded with these decorator 
parsers. These add to or modify ("decorate") the behavior of a single parser.

### Repeatable

Makes a given parser repeatable, by parsing zero or more of its occurrences and
yielding a list of the results.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Repeatable;

$parser = Repeatable::parser('a');
```

Using named constructors:

```php
use Stratadox\Parser\Parsers\Repeatable;
use Stratadox\Parser\Parsers\Text;

$parser = Repeatable::parser(Text::is('a'));
```

Using method:

```php
use function Stratadox\Parser\text;

$parser = text('a')->repeatable();
```

#### Examples

Repeated text:

```php
use Stratadox\Parser\Parsers\Repeatable;

$parser = Repeatable::parser('a');
$result = $parser->parse('aaa');

assert(true === $result->ok());
assert(['a', 'a', 'a'] === $result->data());
```

Unrepeated text:

```php
use Stratadox\Parser\Parsers\Repeatable;

$parser = Repeatable::parser('a');
$result = $parser->parse('a');

assert(true === $result->ok());
assert(['a'] === $result->data());
```

Empty content:

```php
use Stratadox\Parser\Parsers\Repeatable;

$parser = Repeatable::parser('a');
$result = $parser->parse('');

assert(true === $result->ok());
assert([] === $result->data());
```

### Repeatable String

Shortcut that combines [repeatable](#repeatable) and [join](#join).

#### Usage

Using method:

```php
use function Stratadox\Parser\text;

$parser = text('a')->repeatableString();
```

#### Examples

Repeated text:

```php
use function Stratadox\Parser\text;

$parser = text('a')->repeatableString();
$result = $parser->parse('aaa');

assert(true === $result->ok());
assert('aaa' === $result->data());
```

### Map

Applies a function to the data of a successful result.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Map;

$parser = Map::the('foo', $closure);
```

Using named constructors:

```php
use Stratadox\Parser\Parsers\Map;
use Stratadox\Parser\Parsers\Text;

$parser = Map::the(Text::is('foo'), $closure);
```

Using method:

```php
use function Stratadox\Parser\text;

$parser = text('foo')->map($closure);
```

#### Examples

Transforming to upper case:

```php
use Stratadox\Parser\Parsers\Map;
use Stratadox\Parser\Parsers\Text;

$parser = Map::the(Text::is('foo'), fn($result) => strtoupper($result));
$result = $parser->parse('foo');

assert(true === $result->ok());
assert('FOO' === $result->data());
```

Not transforming failed results:

```php
use Stratadox\Parser\Parsers\Map;
use Stratadox\Parser\Parsers\Text;

$parser = Map::the(Text::is('foo'), fn($result) => strtoupper($result));
$result = $parser->parse('bar');

assert(false === $result->ok());
assert('unexpected b' === $result->data());
assert('bar' === $result->unparsed());
```

### Full Map

Applies a function to the result, regardless of whether it was successful or not.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\FullyMap;

$parser = FullyMap::the('foo', $closure);
```

Using named constructors:

```php
use Stratadox\Parser\Parsers\FullyMap;
use Stratadox\Parser\Parsers\Text;

$parser = FullyMap::the(Text::is('foo'), $closure);
```

Using method:

```php
use function Stratadox\Parser\text;

$parser = text('foo')->fullMap($closure);
```

#### Examples

Changing the error:

```php
use Stratadox\Parser\Parsers\FullyMap;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;

$parser = FullyMap::the('foo', fn(Result $r) => $r->ok() ? $r : Error::in('baz'));
$result = $parser->parse('bar');

assert(false === $result->ok());
assert('unexpected b' === $result->data());
assert('baz' === $result->unparsed());
```

Changing error into successful parse:

```php
use Stratadox\Parser\Parsers\FullyMap;
use Stratadox\Parser\Result;
use Stratadox\Parser\Results\Error;

$parser = FullyMap::the(Text::is('foo'), fn(Result $r) => Ok::with('foo', $r->unparsed()));
$result = $parser->parse('bar');

assert(true === $result->ok());
assert('foo' === $result->data());
assert('bar' === $result->unparsed());
```

### Ignore

Requires a successful parsing result, and then ignores it. (Miauw)

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Ignore;

$parser = Ignore::the('foo');
```

Using named constructors:
```php
use Stratadox\Parser\Parsers\Ignore;
use Stratadox\Parser\Parsers\Text;

$parser = Ignore::the(Text::is('foo'));
```

Using method:

```php
use function Stratadox\Parser\text;

$parser = text('foo')->ignore();
```

#### Examples

Ignoring a successful parse:

```php
use Stratadox\Parser\Parsers\Ignore;
use Stratadox\Parser\Parsers\Text;

$parser = Ignore::the(Text::is('foo'));
$result = $parser->parse('foo');

assert(true === $result->ok());
assert(false === $result->use());
assert(null === $result->data());
assert('' === $result->unparsed());
```

Not ignoring a failed result:

```php
use Stratadox\Parser\Parsers\Ignore;
use Stratadox\Parser\Parsers\Text;

$parser = Ignore::the(Text::is('foo'));
$result = $parser->parse('bar');

assert(false === $result->ok());
assert(false === $result->use());
assert('unexpected b' === $result->data());
assert('bar' === $result->unparsed());
```

### Maybe

Returns the result if successful, or an empty "skip this" result otherwise.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Maybe;

$parser = Maybe::that('foo');
```

Using named constructors:

```php
use Stratadox\Parser\Parsers\Maybe;
use Stratadox\Parser\Parsers\Text;

$parser = Maybe::that(Text::is('foo'));
```

Using method:

```php
use function Stratadox\Parser\text;

$parser = text('foo')->maybe();
```

#### Examples

Using a successful result:

```php
use Stratadox\Parser\Parsers\Maybe;
use Stratadox\Parser\Parsers\Text;

$parser = Maybe::that(Text::is('foo'));
$result = $parser->parse('foo bar');

assert(true === $result->ok());
assert(true === $result->use());
assert('foo' === $result->data());
assert(' bar' === $result->unparsed());
```

Ignoring a failed result:

```php
use Stratadox\Parser\Parsers\Maybe;
use Stratadox\Parser\Parsers\Text;

$parser = Maybe::that(Text::is('foo'));
$result = $parser->parse('bar bar');

assert(true === $result->ok());
assert(false === $result->use());
assert(null === $result->data());
assert('bar bar' === $result->unparsed());
```
### Optional

Ignores the parser, yielding a skip result whether the parser succeeds or not.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Optional;
use Stratadox\Parser\Parsers\Text;

$parser = Optional::ignored(Text::is('foo'));
```

Using method:

```php
use function Stratadox\Parser\text;

$parser = text('foo')->optional();
```

#### Examples

Ignoring optional occurrence:

```php
use Stratadox\Parser\Parsers\Optional;
use Stratadox\Parser\Parsers\Text;

$parser = Optional::ignored(Text::is('foo'));
$result = $parser->parse('foo');

assert(true === $result->ok());
assert(false === $result->use());
assert(null === $result->data());
assert('' === $result->unparsed());
```

Ignoring optional non-occurrence:

```php
use Stratadox\Parser\Parsers\Optional;
use Stratadox\Parser\Parsers\Text;

$parser = Optional::ignored(Text::is('foo'));
$result = $parser->parse('bar');

assert(true === $result->ok());
assert(false === $result->use());
assert(null === $result->data());
assert('bar' === $result->unparsed());
```

### Except

Transforms a successful result into a failure if another parser also matches the 
content.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Except;
use Stratadox\Parser\Parsers\Pattern;

$parser = Except::for('4', Pattern::match('\d'));
```

Using method:

```php
use function Stratadox\Parser\pattern;

$parser = pattern('\d')->except('4');
```

#### Examples

Accepting any digit, but failing on 4:

```php
use Stratadox\Parser\Parsers\Except;
use Stratadox\Parser\Parsers\Pattern;

$parser = Except::for('4', Pattern::match('\d'));
$result = $parser->parse('4');

assert(false === $result->ok());
assert('unexpected 4' === $result->data());
assert('4' === $result->unparsed());
```

Accepting 5 as any digit except 4:

```php
use Stratadox\Parser\Parsers\Except;
use Stratadox\Parser\Parsers\Pattern;

$parser = Except::for('4', Pattern::match('\d'));
$result = $parser->parse('5');

assert(true === $result->ok());
assert('5' === $result->data());
assert('' === $result->unparsed());
```

### End

Returns an error result if there is unparsed content remaining after parsing.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\End;
use Stratadox\Parser\Parsers\Text;

$parser = End::with(Text::is('foo'));
```

Using method:

```php
use Stratadox\Parser\Parsers\Text;

$parser = Text::is('foo')->end();
```

#### Examples

Accepting a full match:

```php
use Stratadox\Parser\Parsers\End;
use Stratadox\Parser\Parsers\Pattern;

$parser = End::with(Pattern::match('\d'));
$result = $parser->parse('4');

assert(true === $result->ok());
assert('4' === $result->data());
assert('' === $result->unparsed());
```

Refusing a partial match:

```php
use Stratadox\Parser\Parsers\End;
use Stratadox\Parser\Parsers\Pattern;

$parser = End::with(Pattern::match('\d'));
$result = $parser->parse('42');

assert(false === $result->ok());
assert('unexpected 2' === $result->data());
assert('2' === $result->unparsed());
```

### All or Nothing

Decorator that matches the entire result or nothing at all.
Used to control where the error occurs. No effect on successful results.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\AllOrNothing;
use Stratadox\Parser\Parsers\Text;

$parser = AllOrNothing::in(Text::is('foo'));
```

#### Examples

Resetting the error to the first character:

```php
use Stratadox\Parser\Parsers\AllOrNothing;

$parser = AllOrNothing::in('aaa');
$result = $parser->parse('aab');

assert(false === $result->ok());
assert('unexpected a' === $result->data());
assert('aab' === $result->unparsed());
```

## Combinators

Parsers can be combined using these combinators. 
These parsers take a number of parsers, combining them into one.

### Either / or

Returns the first matching parser of the lot.
If none of the parsers match, returns the error of one that got furthest.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Either;

$parser = Either::of('foo', 'bar', 'baz'));
```

Using named constructors:

```php
use Stratadox\Parser\Parsers\Either;
use Stratadox\Parser\Parsers\Text;

$parser = Either::of(Text::is('foo'), Text::is('bar'), Text::is('baz'));
```

Using method:

```php
use function Stratadox\Parser\text;

$parser = text('foo')->or('bar', 'baz');
```
Using method chain:

```php
use function Stratadox\Parser\text;

$parser = text('foo')->or('bar')->or('baz');
```

#### Examples

Parsing as foo:

```php
use Stratadox\Parser\Parsers\Either;

$parser = Either::of('foo', 'bar', 'baz'));

assert(true === $result->ok());
assert('foo' === $result->data());
assert('' === $result->unparsed());
```

Parsing as bar:

```php
use Stratadox\Parser\Parsers\Either;

$parser = Either::of('foo', 'bar', 'baz'));

assert(true === $result->ok());
assert('bar' === $result->data());
assert('' === $result->unparsed());
```

Refusing abc:

```php
use Stratadox\Parser\Parsers\Either;

$parser = Either::of('foo', 'bar', 'baz'));
$result = $parser->parse('abc');

assert(false === $result->ok());
assert('unexpected a' === $result->data());
assert('abc' === $result->unparsed());
```

### Sequence / andThen

Puts several parsers one after the other.
Leaves ignored results out of the returned list of results.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Parsers\Sequence;
use Stratadox\Parser\Parsers\Text;

$parser = Sequence::of('a', 'b', 'c');
```

Using named constructors:

```php
use Stratadox\Parser\Parsers\Sequence;
use Stratadox\Parser\Parsers\Text;

$parser = Sequence::of(Text::is('a'), Text::is('b'), Text::is('c'));
```

Using method:

```php
use function Stratadox\Parser\text;

$parser = text('a')->andThen('b', 'c');
```

Using method chain:

```php
use function Stratadox\Parser\text;

$parser = text('a')->andThen('b')->andThen('c');
```

#### Examples

Parsing abc as sequence:

```php
use Stratadox\Parser\Parsers\Sequence;

$parser = Sequence::of('a', 'b', 'c');
$result = $parser->parse('abcdef');

assert(true === $result->ok());
assert(['a', 'b', 'c'] === $result->data());
assert('def' === $result->unparsed());
```

Refusing partial matches:

```php
use Stratadox\Parser\Parsers\Sequence;

$parser = Sequence::of('a', 'b', 'c');
$result = $parser->parse('abxyz');

assert(true === $result->ok());
assert('unexpected x' === $result->data());
assert('xyz' === $result->unparsed());
```
Parsing sequences with ignored bits:

```php
use Stratadox\Parser\Parsers\Ignore;
use Stratadox\Parser\Parsers\Sequence;

$parser = Sequence::of(Ignore::the('a'), 'b', 'c');
$result = $parser->parse('abcdef');

assert(true === $result->ok());
assert(['b', 'c'] === $result->data());
assert('def' === $result->unparsed());
```

## Combinator Shortcuts

All the above can be mixed and combined at will. 
To make life easier, there's a bunch of combinator shortcuts for "everyday tasks".

### Between

Matches the parser's content between start and end.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Helpers\Between;
use Stratadox\Parser\Parsers\Pattern;

$parser = Between::these('(', ')', Pattern::match('\d+'));
```

Using named constructors:

```php
use Stratadox\Parser\Helpers\Between;
use Stratadox\Parser\Parsers\Pattern;
use Stratadox\Parser\Parsers\Text;

$parser = Between::these(Text::is('('), Text::is(')'), Pattern::match('\d+'));
```

Using method:

```php
use function Stratadox\Parser\pattern;

$parser = pattern('\d+')->between('(', ')');
```

Using method with single parameter:

```php
use function Stratadox\Parser\pattern;

$parser = pattern('[a-z]+')->between('"');
```

#### Examples

Parsing an integer between braces:

```php
use Stratadox\Parser\Helpers\Between;
use Stratadox\Parser\Parsers\Pattern;

$parser = Between::these('(', ')', Pattern::match('\d+'));
$result = $parser->parse('(123)');

assert(true === $result->ok());
assert('123' === $result->data());
assert('' === $result->unparsed());
```

Refusing an integer without braces:

```php
use Stratadox\Parser\Helpers\Between;
use Stratadox\Parser\Parsers\Pattern;

$parser = Between::these('(', ')', Pattern::match('\d+'));
$result = $parser->parse('123');

assert(true === $result->ok());
assert('unexpected 1' === $result->data());
assert('123' === $result->unparsed());
```

### Between Escaped

Matches unescaped content between start and end.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Helpers\Between;

$parser = Between::escaped($from, $to, $escape);
```

#### Examples

Parsing a string with escaped quotes:

```php
use Stratadox\Parser\Helpers\Between;

$parser = Between::escaped('"', '"', '!');
$result = $parser->parse('"foo!"bar!!"');

assert(true === $result->ok());
assert('foo"bar!' === $result->data());
assert('' === $result->unparsed());
```

Using a different escape for the escape sequence:

```php
use Stratadox\Parser\Helpers\Between;

$parser = Between::escaped('"', '"', '!?', '_');
$result = $parser->parse('"foo!?"bar_!?"');

assert(true === $result->ok());
assert('foo"bar!?' === $result->data());
assert('' === $result->unparsed());
```

### Splitting

#### Optional split

Splits content based on a separator.
Yields one or more results.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::optional(',', Pattern::match('\d+'));
```

Using method:

```php
use function Stratadox\Parser\pattern;

$parser = pattern('\d+')->split(',');
```

#### Examples

Splitting integers with commas:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::optional(',', Pattern::match('\d+'));
$result = $parser->parse('12,16,1,5646');

assert(true === $result->ok());
assert(['12', '16', '1', '5646'] === $result->data());
assert('' === $result->unparsed());
```

Splitting a single integer with (absent) commas:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::optional(',', Pattern::match('\d+'));
$result = $parser->parse('12');

assert(true === $result->ok());
assert(['12'] === $result->data());
assert('' === $result->unparsed());
```

#### Mandatory split

Splits content based on a separator.
Yields two or more results.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::with(',', Pattern::match('\d+'));
```

Using method:

```php
use function Stratadox\Parser\pattern;

$parser = pattern('\d+')->mustSplit(',');
```

#### Examples

Splitting integers with commas:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::with(',', Pattern::match('\d+'));
$result = $parser->parse('12,16,1,5646');

assert(true === $result->ok());
assert(['12', '16', '1', '5646'] === $result->data());
assert('' === $result->unparsed());
```

Refusing content without separator:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::with(',', Pattern::match('\d+'));
$result = $parser->parse('12');

assert(false === $result->ok());
assert('unexpected end' === $result->data());
assert('' === $result->unparsed());
```

#### Mandatory split with separator

Splits content based on a separator, keeping the separator.
Yields a structure like `{delimiter: [left, right]}`, unless mapped differently.

#### Usage

Using named constructors:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Either;
use Stratadox\Parser\Parsers\Pattern;
use Stratadox\Parser\Parsers\Text;

$parser = Split::keep(
    Either::of(Text::is('*'), Text::is('/')),
    Pattern::match('\d+')
);
```

Using named constructor with shortcuts:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::keep(['*', '/'], Pattern::match('\d+'));
```

Using named constructor with shortcuts and mapping:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::keep(['*', '/'], Pattern::match('\d+'), $closure);
```

Using method:

```php
use function Stratadox\Parser\pattern;

$parser = pattern('\d+')->keepSplit(['*', '/']);
```

Using method with mapping:

```php
use function Stratadox\Parser\pattern;

$parser = pattern('\d+')->keepSplit(['*', '/'], $closure);
```

#### Examples

Splitting multiplication and division, keeping the operator:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::keep(['*', '/'], Pattern::match('\d+'));
$result = $parser->parse('1*2/3');

assert(true === $result->ok());
assert(['/' => [['*' => ['1', '2']], '3']] === $result->data());
```

Splitting multiplication and division, mapping the result:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::keep(['*', '/'], Pattern::match('\d+'), fn($op, $l, $r) => [
    'op' => $op,
    'arg' => [$l, $r],
]);
$result = $parser->parse('1*2/3');

assert(true === $result->ok());
assert([
    'op' => '/',
    'arg' => [
        [
            'op' => '*',
            'arg' => ['1', '2'],
        ],
        '3',
    ],
] === $result->data());
```

Refusing content without separator:

```php
use Stratadox\Parser\Helpers\Split;
use Stratadox\Parser\Parsers\Pattern;

$parser = Split::keep(['*', '/'], Pattern::match('\d+'));
$result = $parser->parse('1');

assert(false === $result->ok());
assert('unexpected end' === $result->data());
assert('' === $result->unparsed());
```

## Helper Decorators

There's several additional helpers, which are essentially mapping shortcuts.

### Join

Implodes the array result into a string.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Helpers\Join;
use Stratadox\Parser\Parsers\Sequence;

$parser = Join::the(Sequence::of('a', 'b', 'c'));
```

Using named constructor with glue:

```php
use Stratadox\Parser\Helpers\Join;
use Stratadox\Parser\Parsers\Sequence;

$parser = Join::with(';', Sequence::of('a', 'b', 'c'));
```
Using method:

```php
use Stratadox\Parser\Parsers\Sequence;

$parser = Sequence::of('a', 'b', 'c')->join();
```

Using method with glue:

```php
use Stratadox\Parser\Parsers\Sequence;

$parser = Sequence::of('a', 'b', 'c')->join(';');
```

#### Examples

Joining a sequence:

```php
use Stratadox\Parser\Parsers\Sequence;

$parser = Sequence::of('a', 'b', 'c')->join(';');
$result = $parser->parse('abc');

assert(true === $result->ok());
assert('a;b;c' === $result->data());
```

No transformations on text result:

```php
use Stratadox\Parser\Parsers\Text;

$parser = Text::is('abc')->join(';');
$result = $parser->parse('abc');

assert(true === $result->ok());
assert('abc' === $result->data());
```

### Non-empty

Refuses `empty` results.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Helpers\NonEmpty;
use Stratadox\Parser\Parsers\Pattern;

$parser = NonEmpty::result(Pattern::match('\d+'));
```

Using method:

```php
use Stratadox\Parser\Parsers\Pattern;

$parser = Pattern::match('\d+')->nonEmpty();
```

### At least

Refuses array results with fewer than x entries.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Helpers\AtLeast;
use Stratadox\Parser\Parsers\Repeatable;

$parser = AtLeast::results(5, Repeatable::parser('a'));
```

Using method:

```php
use Stratadox\Parser\Parsers\Repeatable;

$parser = Repeatable::parser('a')->atLeast(5);
```

#### Examples

Refusing results with fewer than 5 entries:

```php
use Stratadox\Parser\Helpers\AtLeast;
use Stratadox\Parser\Parsers\Repeatable;

$parser = AtLeast::results(5, Repeatable::parser('a'));
$result = $parser->parse('aaaa');

assert(false === $result->ok());
assert('unexpected end' === $result->data());
```

### At most

Refuses array results with more than x entries.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Helpers\AtMost;
use Stratadox\Parser\Parsers\Repeatable;

$parser = AtMost::results(5, Repeatable::parser('a'));
```

Using method:

```php
use Stratadox\Parser\Parsers\Repeatable;

$parser = Repeatable::parser('a')->atMost(5);
```

#### Examples

Refusing results with more than 5 entries:

```php
use Stratadox\Parser\Helpers\AtMost;
use Stratadox\Parser\Parsers\Repeatable;

$parser = AtMost::results(5, Repeatable::parser('a'));
$result = $parser->parse('aaaaaa');

assert(false === $result->ok());
assert('unexpected a' === $result->data());
```

## Containers

To enable lazy parsers (and/or to provide a structure), different containers are 
available.

### Lazy Container

Manages lazy loading, essential for recursive parsers.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Containers\Lazy;

$lazy = Lazy::container();
```

#### Examples

Parsing a recursive definition:

```php
use Stratadox\Parser\Containers\Lazy;

$lazy = Lazy::container();
$lazy['parser'] = $lazy['parser']->between('(', ')')->or('z');

$result = $lazy['parser']->parse('(((z)))');

assert(true === $result->ok());
assert('z' === $result->data());
```

### Eager Container

A basic typed list of regular parsers.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Containers\Eager;

$eager = Eager::container();
```

#### Examples

It's just a simple container:

```php
use Stratadox\Parser\Containers\Eager;
use Stratadox\Parser\Parsers\Text;

$eager = Eager::container();
$eager['parser'] = Text::is('foo');

$result = $eager['parser']->parse('foo');

assert(true === $result->ok());
assert('foo' === $result->data());
```

### Recursion-Safe Lazy Container

Prevents infinite looping on left-recursion.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Containers\Lazy;
use Stratadox\Parser\Containers\Limited;

$lazy = Limited::recursion(Lazy::container());
```

#### Examples

Parsing despite left-recursion:

```php
use Stratadox\Parser\Containers\Lazy;
use Stratadox\Parser\Containers\Limited;

$lazy = Limited::recursion(Lazy::container());
$lazy['parser'] = $lazy['parser']->andThen('a')->or('z')->split(',');

$result = $lazy['parser']->parse('z,z,z');

assert(true === $result->ok());
assert(['z', 'z', 'z'] === $result->data());
```

### Grammar Container

Mixes lazy and eager containers.

#### Usage

Using named constructor:

```php
use Stratadox\Parser\Containers\Grammar;

$grammar = Grammar::container();
```

Using named constructor and `$lazy` syntax:

```php
use Stratadox\Parser\Containers\Grammar;
use Stratadox\Parser\Containers\Lazy;

$grammar = Grammar::with($lazy = Lazy::container());
```

#### Examples

```php
use Stratadox\Parser\Containers\Grammar;
use Stratadox\Parser\Containers\Lazy;
use function Stratadox\Parser\text;

$grammar = Grammar::with($lazy = Lazy::container());

$grammar['a|b'] = text('a')->or('b');
$lazy['(a|b)'] = $grammar['(a|b)']->between('(', ')')->or($grammar['(a|b)']);

$result = $lazy['(a|b)']->parse('((((((b))))))');

assert(true === $result->ok());
assert('b' === $result->data());
```

```php
use Stratadox\Parser\Containers\Grammar;
use Stratadox\Parser\Containers\Lazy;
use function Stratadox\Parser\text;

$grammar = Grammar::with($lazy = Lazy::container());

$grammar['a|b'] = text('a')->or('b');
$lazy['(a|b)'] = $grammar['(a|b)']->between('(', ')')->or($grammar['(a|b)']);

$result = $lazy['(a|b)']->parse('(a)');

assert(true === $result->ok());
assert('a' === $result->data());
```
