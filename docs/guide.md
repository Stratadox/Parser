# Parser Combinator Usage Guide
Welcome to parser combinators!

For the details of each individual parser, see [the reference](reference.md).

## Contents

- [Preface: Results](#preface-results)
- [Chapter 1: Introduction](#chapter-1-introduction)
  - [1.1: Greeting parser](#11-greeting-parser)
  - [1.2: Verb parser](#12-verb-parser)
  - [1.3: Name parser](#13-name-parser)
  - [1.4: Suffix parser](#14-suffix-parser)
- [Chapter 2: Optional, maybe](#chapter-2-optional-maybe)
  - [2.1: Ignored results](#21-ignored-results)
  - [2.2: Maybe a parser](#22-maybe-a-parser)
  - [2.3: Optional parsers](#23-optional-parsers)
- [Chapter 3: Mapping](#chapter-3-mapping)
  - [3.1: Mapping the result data](#31-mapping-the-result-data)
  - [3.2: Joining the resulting array](#32-joining-the-resulting-array)
  - [3.3: At least 2](#33-at-least-2)

Todo:

- [Chapter 4: Between](#chapter-4-between)
  - [4.1: Between braces](#41-between-braces) 
  - [4.2: Between escapable quotes](#42-between-escapable-quotes)
- [Chapter 5: Splitting](#chapter-5-splitting)
  - [5.1: Optional split](#51-optional-split)
  - [5.2: Mandatory split](#52-mandatory-split)
  - [5.3: Keep split](#53-keep-split)
- [Chapter 6: Lazy](#chapter-6-lazy)
  - [6.1: Lazy loading](#61-lazy-loading)
  - [6.2: Left-recursion](#62-left-recursion)
- [Chapter 7: Grammar](#chapter-7-grammar)

## Preface: Results

Each parser returns a Result. This result can be an Ok, or an Error. 
(Or a Skip, but that's mostly for internal use.)
To see whether the input was successfully parsed, use:

`if ($result->ok())`

The parsed data can be retrieved using:

`$result->data()`

## Chapter 1: Introduction

Parser combinators are parsers that are composed of parsers.
Each individual building block is relatively simple. 
When combined, these simple building blocks can become powerful parsers.

In this chapter of the guide, we're going to parse some *introductions*.
Let's say, for instance, we wish to parse basic sentences, such as:

- `Hello, my name is Alice`
- `Hi, I'm Bob. :)`
- `Hey there, I'm called Charlie!`

We can split these sentences into four parts: the greeting, the verb, the name 
and the optional suffix.

The parsers used below are also available as [unit test](../tests/Guide/Chapter_1_Introduction.php).

### 1.1 Greeting parser

The greetings we've seen are "Hello, " or "Hi, " or "Hey there, ". 

Individually, each of those can be parsed with a simple [*text parser*](reference.md#text).
Those text parsers can then be combined using an [*or parser*](reference.md#either--or).

Using this library, that can be written simply as: 
`text("Hello, ")->or("Hi, ")->or("Hey there, ");`.

At this point, we can already parse a part of the introductions.
Since we've only defined a parser for the first part, we'd get a result like this:

```php
<?php
use function Stratadox\Parser\text;

$greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");

$result1 = $greeting->parse("Hello, my name is Alice");

assert("Hello, " === $result1->data());
assert("my name is Alice" === $result1->unparsed());

$result2 = $greeting->parse("Hey there, I'm called Charlie!");

assert("Hey there, " === $result2->data());
assert("I'm called Charlie!" === $result2->unparsed());
```

As we can see, our greeting parser produces a result with the particular greeting 
as data, and the remaining content as "unparsed". 
This unparsed content can then be consumed by our (upcoming) verb parser.

### 1.2 Verb parser

The three supported verbs are "my name is", "I'm" and "I'm called".
In order to properly parse this, we'll need to change the order of these last two.
The [*or parser*](reference.md#either--or) will use the first matching parser, 
so in the current order we'd end up with only "I'm" when the actual verb is "I'm 
called".

To prevent that, we'd end up with 
`text("my name is ")->or("I'm called ")->or("I'm ");`.

When we combine the greeting parser with the verb parser, it might look like this:

```php
<?php
use function Stratadox\Parser\text;

$greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
$verb = text("my name is ")->or("I'm called ")->or("I'm ");
$introduction = $greeting->andThen($verb);

$result = $introduction->parse("Hello, my name is Alice");

assert(["Hello, ", "my name is "] === $result->data());
assert("Alice" === $result->unparsed());
```

You may have noticed suddenly we didn't get a string as data, but an array.
This is because the [*andThen*](reference.md#sequence--andthen) combinator puts 
the parsers in sequence, and outputs the results as an array.

### 1.3 Name parser

For the purposes of this guide, lets assume names simply always follow the regular 
expression `[A-Za-z]+`.

To parse names, we'll simply use the [*pattern parser*](reference.md#pattern):
`pattern("[A-Za-z]+");`.

We can simply append this parser:

```php
<?php
use function Stratadox\Parser\pattern;
use function Stratadox\Parser\text;

$greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
$verb = text("my name is ")->or("I'm called ")->or("I'm ");
$name = pattern("[A-Za-z]+");
$introduction = $greeting->andThen($verb)->andThen($name);

$result1 = $introduction->parse("Hello, my name is Alice");

assert(["Hello, ", "my name is ", "Alice"] === $result1->data());
assert("" === $result1->unparsed());

$result2 = $greeting->parse("Hi, I'm Bob. :)");

assert(["Hi, ", "I'm ", "Bob"] === $result2->data());
assert(". :)" === $result->unparsed());
```

### 1.4: Suffix parser

Oh, no! Turns out our assumption was wrong! 
We hadn't considered these two extra cases:

- `Hey there, my name is Tom Bombadil. :)`
- `Hi, I'm called จอห์น ฟรัม`

Our parser is suddenly insufficient: In the first scenario, we'd only get Tom, 
not Bombadil, and the second scenario would return an error "unexpected จ".

To improve the parser, so that we can allow for *any* name, we're going to use 
some tricks.

First, lets define a suffix parser. This is either "!", ". :)" or "".
We'll also declare that this is the [end of the content](reference.md#end):
`text(". :)")->or("!")->or("")->end()`

Now, we can redefine the name parser as being [anything](reference.md#any-symbol).
Anything [except](reference.md#except) for the suffix, that is.
Finally, we'll make it a [repeatable string](reference.md#repeatable-string):
`any()->except($suffix)->repeatableString()`

The combined result parses all four parts:

```php
<?php
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

$greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
$verb = text("my name is ")->or("I'm called ")->or("I'm ");
$suffix = text(". :)")->or("!")->or("")->end();
$name = any()->except($suffix)->repeatableString();
$introduction = $greeting->andThen($verb, $name, $suffix);
```

`Hey there, my name is Tom Bombadil. :)` 
becomes `["Hey there, ", "my name is ", "Tom Bombadil", ". :)"]`

`Hi, I'm called จอห์น ฟรัม` 
becomes `["Hi, ", "I'm called ", "จอห์น ฟรัม", ""]`

`Hello, my name is Alice` 
becomes `["Hello, ", "my name is ", "Alice", ""]`

`Hi, I'm Bob. :)` 
becomes `["Hi, ", "I'm ", "Bob", ". :)"]`

`Hey there, I'm called Charlie!` 
becomes `["Hey there, ", "I'm called ", "Charlie", "!"]`

## Chapter 2: Optional, maybe

In [the first chapter](#chapter-1-introduction) we made a parser that splits the 
input into four parts.

Our objective for this chapter is to clean up our output a bit. 

The parsers used below are also available as [unit test](../tests/Guide/Chapter_2_Optional_Maybe.php).

### 2.1: Ignored results

For starters, lets say we're not actually interested in the verb that's used.
We can skip the verb parser by marking it as [*ignored*](reference.md#ignore):

```php
<?php
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

$greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
$verb = text("my name is ")->or("I'm called ")->or("I'm ")->ignore();
$suffix = text(". :)")->or("!")->or("")->end();
$name = any()->except($suffix)->repeatableString();
$introduction = $greeting->andThen($verb, $name, $suffix);

$result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

assert(["Hey there, ", "Tom Bombadil", ". :)"] === $result->data());
```

An alternative use-case is to cut off only part of a parser:

```php
<?php
use Stratadox\Parser\Parsers\Ignore;
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

$greeting = text("Hello")->or("Hi")->or("Hey there")->andThen(Ignore::the(', '));
$verb = text("my name is ")->or("I'm called ")->or("I'm ");
$suffix = text(". :)")->or("!")->or("")->end();
$name = any()->except($suffix)->repeatableString();
$introduction = $greeting->andThen($verb, $name, $suffix);

$result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

assert(["Hey there", "my name is ", "Tom Bombadil", ". :)"] === $result->data());
```

### 2.2: Maybe a parser

Next up, lets get rid of that empty string when there is no suffix. 
Instead of using `or("")`, we can mark it as a [*maybe*](reference.md#maybe):

```php
<?php
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

$greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ");
$verb = text("my name is ")->or("I'm called ")->or("I'm ")->ignore();
$suffix = text(". :)")->or("!")->maybe()->end();
$name = any()->except($suffix)->repeatableString();
$introduction = $greeting->andThen($verb, $name, $suffix);

$result = $introduction->parse("Hello, my name is Alice");

assert(["Hello, ", "Alice"] === $result->data());

$result = $introduction->parse("Hi, I'm Bob. :)");

assert(["Hi, ", "Bob", ". :)"] === $result->data());
```

### 2.3: Optional parsers

Let's finish this chapter by making the greeting [*optional*](reference.md#optional): 

```php
<?php
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

$greeting = text("Hello, ")->or("Hi, ")->or("Hey there, ")->optional();
$verb = text("my name is ")->or("I'm called ")->or("I'm ")->ignore();
$suffix = text(". :)")->or("!")->maybe()->end();
$name = any()->except($suffix)->repeatableString();
$introduction = $greeting->andThen($verb, $name, $suffix);

$result = $introduction->parse("Hello, my name is Alice");

assert(["Alice"] === $result->data());

$result = $introduction->parse("I'm Bob. :)");

assert(["Bob", ". :)"] === $result->data());
```

## Chapter 3: Mapping

In [chapter 2](#chapter-2-optional-maybe) we made some of our parsers optional, 
and ignored the results of others. 

This third chapter will focus on the various ways in which we can modify the 
results after parsing.

We'll also undo most of the filtering from the last chapter for now, using as 
baseline:

```php
<?php
use Stratadox\Parser\Parsers\Ignore;
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

$greeting = text("Hello")->or("Hi", "Hey there")->andThen(Ignore::the(', '));
$verb = text("my name is ")->or("I'm called ", "I'm ");
$suffix = text(". :)")->or("!")->maybe()->end();
$name = any()->except($suffix)->repeatableString();
$introduction = $greeting->andThen($verb, $name, $suffix);
```

The parsers used below are also available as [unit test](../tests/Guide/Chapter_3_Mapping.php).

### 3.1: Mapping the result data

For now, the result of our parser is something like `["Hello", "my name is ", "Alice"]` 
or `["Hey there", "my name is ", "Tom Bombadil", ". :)"]`

But what if we'd like a different output? 
This is where [mapping](reference.md#map) comes in.
Let's start with a simple one, and make the greeting uppercase:
`$greeting->map(fn($x) => strtoupper($x))`

Within the example, that'd look like this:

```php
<?php
use Stratadox\Parser\Parsers\Ignore;
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

$greeting = text("Hello")->or("Hi", "Hey there")
    ->map(fn($x) => strtoupper($x))
    ->andThen(Ignore::the(', '));
$verb = text("my name is ")->or("I'm called ", "I'm ");
$suffix = text(". :)")->or("!")->maybe()->end();
$name = any()->except($suffix)->repeatableString();
$introduction = $greeting->andThen($verb, $name, $suffix);

$result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

assert(["HEY THERE", "my name is ", "Tom Bombadil", ". :)"] === $result->data());
```

We can apply mappings at multiple "levels" of the combined parser. For example:

```php
<?php
use Stratadox\Parser\Parsers\Ignore;
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

$greeting = text("Hello")->or("Hi", "Hey there")
    ->map(fn($x) => strtolower($x))
    ->andThen(Ignore::the(', '));
$verb = text("my name is ")->or("I'm called ", "I'm ")
    ->map(fn($x) => trim($x));
$suffix = text(". :)")->or("!")->maybe()->end();
$name = any()->except($suffix)->repeatableString();
$introduction = $greeting->andThen($verb, $name, $suffix)
    ->map(fn($x) => [
        'greeting' => $x[0],
        'verb' => $x[1],
        'name' => $x[2],
        'suffix' => $x[3] ?? null,
    ]);

$result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

assert([
    'greeting' => "hey there",
    'verb' => "my name is",
    'name' => "Tom Bombadil",
    'suffix' => ". :)",
] === $result->data());
```

By default, the data will only be mapped if the parser succeeds.
It's also possible to [map the full result](reference.md#full-map) instead of 
just the data.

### 3.2: Joining the resulting array

Several parsers (such as the [sequence parser](reference.md#sequence--andthen) 
and the [repeatable parser](reference.md#repeatable)) return an array.
To easily transform them back to a string, we can use [*join*](reference.md#join):
`$parser->join()` (or `parser->join($glue)`).

Here's what our example parser would look like if we'd join the greeting and verb 
together with some glue:

```php
<?php
use Stratadox\Parser\Parsers\Ignore;
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

$greeting = text("Hello")->or("Hi", "Hey there")
    ->map(fn($x) => strtolower($x))
    ->andThen(Ignore::the(', '));
$verb = text("my name is ")->or("I'm called ", "I'm ")
    ->map(fn($x) => trim($x));
$suffix = text(". :)")->or("!")->maybe()->end();
$name = any()->except($suffix)->repeatableString();
$introduction = $greeting->andThen($verb)->join(' / ')
    ->andThen($name, $suffix)
    ->map(fn($x) => [
        'greeting' => $x[0],
        'name' => $x[1],
        'suffix' => $x[2] ?? null,
    ]);

$result = $introduction->parse("Hey there, my name is Tom Bombadil. :)");

assert([
    'greeting' => "hey there / my name is",
    'name' => "Tom Bombadil",
    'suffix' => ". :)",
] === $result->data());
```

(We've been cheating a little, we kind of already used *join*... the 
`->repeatableString()` we used for the name is basically a shortcut for: 
`->repeatable()->join()`)

### 3.3: At least 2

Time for a new requirement!
Our imaginary product owner just told us names must at least be 2 characters 
long.

We can do so by changing the aforementioned `->repeatableString()` into
`->repeatable()->join()`, and then insert [*atLeast*](reference.md#at-least):
`->repeatable()->atLeast(2)->join()`

When applying this to the baseline of our chapter, we get:

```php
<?php
use Stratadox\Parser\Parsers\Ignore;
use function Stratadox\Parser\any;
use function Stratadox\Parser\text;

$greeting = text("Hello")->or("Hi", "Hey there")->andThen(Ignore::the(', '));
$verb = text("my name is ")->or("I'm called ", "I'm ");
$suffix = text(". :)")->or("!")->maybe()->end();
$name = any()->except($suffix)->repeatable()
    ->atLeast(2)
    ->join();
$introduction = $greeting->andThen($verb, $name, $suffix);

$result = $introduction->parse("Hey there, my name is A. :)");

assert(false === $result->ok());
assert('unexpected .', $result->data());
```

Its counterpart, [*atMost*](reference.md#at-most), works exactly the same way.

Validation modifiers such as these can be extra powerful when used as part of an 
[either / or](reference.md#either--or) construction. 
The example below is slightly contrived, but hopefully shows the potential:

```php
<?php
use function Stratadox\Parser\text;

$parser = text('a')->repeatable()->atLeast(3)->atMost(5)
    ->or(
        text('a')->repeatable()->ignore()
            ->andThen(text('b')->repeatable()->atLeast(5))
            ->first()
    );

$result1 = $parser->parse('aaaabbbbbbbbb');

assert(['a', 'a', 'a', 'a'] === $result1->data());

$result2 = $parser->parse('aaaaaaaabbbbb');

assert(['b', 'b', 'b', 'b', 'b'] === $result1->data());
```

So, what happened here? 

The first input satisfied both our rules. 
The text parser parsed four `a`'s: that's at least 3 and at most 5, so the 
result is a list of four times `'a'`.

Our second input, however, had too many `a`'s.
As such, the first branch of the [either / or](reference.md#either--or) failed, 
and the second branch was considered. 
That branch ignores all the `a`'s, and then matches all the `b`'s if there's at 
least five of them.

## Chapter 4: Between

Todo

### 4.1: Between braces

Todo

### 4.2: Between escapable quotes

Todo

## Chapter 5: Splitting

Todo

### 5.1: Optional split

Todo

### 5.2: Mandatory split

Todo

### 5.3: Keep split

Todo

## Chapter 6: Lazy

Todo

### 6.1: Lazy loading

Todo

### 6.2: Left-recursion

Todo

## Chapter 7: Grammar

Todo
