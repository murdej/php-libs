# StringWalk

`Murdej\StringWalk`

A stateful cursor over a string, useful for writing small hand-rolled parsers/scanners: search
forward for needles (plain strings or regular expressions), remember positions ("marks"), jump
back to them, and extract substrings between marks.

API reference: [`Murdej-StringWalk`](../api-docs/api/classes/Murdej-StringWalk.html)

## Properties

| Property | Type | Description |
|---|---|---|
| `$src` | `string` | The string being scanned. |
| `$pos` | `int` | Current cursor position. |
| `$lastChunk` | `string\|null` | The needle/match found by the last `findNext*`/`findCurrent` call. |
| `$lastChunkNum` | `int\|null` | Index (within the needles array) of the last found chunk. |
| `$lastMarkName` | `mixed` | Name of the last mark saved via `saveMark()`. |
| `$lastMatch` | `array\|null` | Full match array (with offsets) from the last `findNextPreg()` call. |
| `$debug` | `bool` | When `true`, traces calls via `$debugCallback`. |
| `$debugCallback` | `callable` | Called as `($method, $msg)` for tracing; defaults to an `echo`-based logger. |

`const End = '__end'` — the name of the mark automatically set to the end of the string in the
constructor.

## Constructor

```php
public function __construct($src)
```

Wraps `$src` and initializes a mark named `StringWalk::End` pointing at the end of the string.

## Methods

### `findNext($needles, $cs = true): ?string`

Searches forward from the current position for the earliest occurrence of any of `$needles`
(a string or array of strings), moves `$pos` to that occurrence, and returns the matched needle
(or `null` if none found). `$cs` controls case sensitivity (`true` = case-insensitive search via
`stripos`, despite the name — see source).

### `findNextPreg($pattern): ?string`

Like `findNext()`, but searches using a PCRE `$pattern` (via `preg_match(..., PREG_OFFSET_CAPTURE, $this->pos)`).
On success, moves `$pos` to the match, stores the full match array in `$lastMatch`, and returns
the matched text.

### `findCurrent($needles): ?string`

Checks (without moving the cursor forward otherwise) whether the string starting at the current
position begins with one of `$needles`; returns the first one that matches, or `null`.

### `startsWith(string $str): bool`

`true` if the remaining (unconsumed) string starts with `$str`.

### `saveMark($n = '.'): void`

Remembers the current position under the name `$n` (any scalar).

### `goto($n): void`

Moves the cursor back to a previously saved mark `$n`.

### `toEndChunk(): void`

Advances the cursor past the end of `$lastChunk` (i.e. `$pos += strlen($lastChunk)`).

### `substr(?string $f = null, ?string $t = null): string`

Extracts a substring of `$src` between two marks:

- `substr()` — from the last saved mark to the current position.
- `substr($mark)` — from `$mark` to the current position.
- `substr($mark1, $mark2)` — from `$mark1` to `$mark2`.

### `srcLen(): int`

Returns `strlen($this->src)`.

### `getLeftSrc($max = null): string`

Returns the remaining (unconsumed) part of the string from the current position, optionally
limited to `$max` characters.

## Typical usage

```php
$w = new StringWalk('SELECT name FROM users WHERE id = 1');
$w->findNext('FROM');
$w->toEndChunk();      // move past "FROM"
$w->saveMark('start');
$w->findNext('WHERE');
$table = trim($w->substr('start')); // 'users'
```
