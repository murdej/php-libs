# ProcI

`Murdej\ProcI`

A fluent, chainable wrapper around an array/iterable that provides jQuery/Collection-style
operations: mapping, filtering, ordering, grouping ("structuring"), reducing, uniqueness,
cartesian products and more. Most methods mutate and return `$this` so calls can be chained; call
[`toArray()`](#toarray-array) at the end to get a plain array back.

A global shorthand function `PrI($src)` (defined in the same file, outside the class) is
equivalent to `new ProcI($src)`.

API reference: [`Murdej-ProcI`](../api-docs/api/classes/Murdej-ProcI.html)

## Creating an instance

```php
use Murdej\ProcI;
use function Murdej\PrI;

$p = new ProcI($array);
$p = ProcI::from($array);   // same as above
$p = PrI($array);           // shorthand
```

### `static from(...$srcs): self`

Creates a new `ProcI`. With a single argument, wraps it as-is (keys preserved). With multiple
iterables, concatenates all of their **values** into one zero-indexed array (keys are not
preserved/merged).

### `static fromCartesian(array $arrays, ?callable $callback = null): ProcI`

Builds a `ProcI` from the [cartesian product](#static-cartesianproductarray-arrays-callback-null-array)
of the given arrays.

## String callbacks

Most methods that accept a `$callback` also accept a compact string form instead of a closure,
via `ProcI::prepareCallback()`. The syntax (applied to each item `$v`, starting as `$v = $item`):

| Syntax | Meaning |
|---|---|
| `.` | identity — returns the item itself |
| `.field` | property access: `$v = $v->field` |
| `[field` | array access: `$v = $v['field']` |
| `.a.b`, `.a[b`, `[a.b`, ... | any number of chained `.`/`[` accessors, e.g. `.a[b` = `$v->a['b']` |
| `?.field`, `?[field` | same as above, but short-circuits to `null` if `$v` is already `null` at that step |
| `(int)`, `(float)`, `(string)`, `(bool)` prefix | casts the final value with `settype()`, e.g. `'(int).a'` |
| `-` prefix | negates the final (numeric) value, e.g. `'-.a'` |

```php
ProcI::from($rows)->map('.a[b');      // $row->a['b'] for every row
ProcI::from($rows)->orderBy('-.b');   // order by -$row->b (i.e. descending by $row->b)
ProcI::from($rows)->map('(int).a');   // (int) $row->a
```

### `static prepareCallback($callback)`

Turns a string in the syntax above into a `Closure`. If `$callback` is already a `callable`
(closure, `[$obj, 'method']`, ...) or not a string, it is returned unchanged (`null` is also
passed through unchanged).

## Methods

### `map($itemCallback, $keyCallback = null): $this`

Maps every item (and optionally every key) through the given callback(s)
(`fn($item, $key) => ...`).

### `each($itemCallback): $this`

Calls the callback for every item (for side effects); does not modify the collection.

### `orderBy(...$callbacks): $this`

Sorts items (preserving keys) by one or more callbacks, in order — later callbacks act as
tie-breakers for equal earlier ones.

```php
ProcI::from($rows)->orderBy('.lastName', '-.age');
```

### `filter(string|array|callable $callback): $this`

Keeps only items for which `$callback($item, $key)` is truthy.

### `filterKey(array $keys, bool $strict = false): $this`

Keeps only items whose key is in `$keys` (`$strict` uses `===` via `in_array()`'s strict mode).

### `reverse(): $this`

Reverses the order of elements (keys are preserved, like `array_reverse($arr, true)`).

### `unique(string|array|callable|null $compareBy = null, \Closure|callable|null $selectCallback = null): $this`

Removes duplicates. Without `$compareBy`, behaves like `array_unique()`. With `$compareBy`, items
are grouped by the value it produces for each item; by default the *first* item of each group is
kept, or, if `$selectCallback` is given, `$selectCallback($itemsInGroup)` picks which one to keep.

```php
// keep, per distinct `.a`, the item with the highest `.b`
ProcI::from($rows)->unique('.a', fn($items) => ProcI::from($items)->orderBy('-.b')->first());
```

### `struct(...$callbacks): $this`

Re-keys/groups the collection according to one or more key-callbacks (see `mapStruct()`).

### `mapStruct($mapCallback, ...$callbacks): $this`

The general form behind `struct()`. For every item, each callback in `$callbacks` computes a key
for one nesting level (pass `null` for a callback to just append to a numeric-indexed array at
that level instead of using a key), building up a nested array. At the deepest level, the item
(optionally transformed by `$mapCallback`) is stored.

```php
// group rows by department, then by team
ProcI::from($rows)->struct('.department', '.team')->toArray();
// ['sales' => ['east' => [...rows...], 'west' => [...]], ...]
```

### `reduceStruct($reduceCallback, ...$callbacks): $this`

Like `mapStruct()`, but the value stored at the deepest level comes from
`$reduceCallback($item, $key)` instead of the item itself. *(marked `@todo` in the source — API
may still change.)*

### `shuffle(): $this`

Randomizes the order of elements (via `shuffle()`, so keys are **not** preserved).

### `slice(int $offset, int $length): $this`

Keeps only the given slice of elements (`array_slice()`).

### `reduce($callback, $initial): mixed`

Runs `array_reduce()` over the collection and **returns the reduced value directly** (unlike
other methods, it does not return `$this`/modify the collection).

### `first($callback = null, $default = null): mixed`

Returns the first element. If `$callback` is given, returns the first element for which
`$callback($item, $key)` is truthy, or `$default` if none matches (or the collection is empty).

### `toArray(): array`

Returns the underlying data as a plain PHP array (converting from a plain iterable if needed).

### `values(): $this`

Re-indexes the collection numerically, discarding keys (`array_values()`).

### `keys(): $this`

Replaces the collection's values with its keys (`array_keys()`).

### `reduceBy(array $fields, array $byFields): $this`

Groups rows by `$byFields` (each a string callback/callable used to compute the group key) and,
per group, reduces one or more [`ProcIReduceField`](ProcIReduceField.md) definitions into
aggregate values.

```php
ProcI::from($rows)->reduceBy(
    [
        new ProcIReduceField('sum', '[c', fn($a, $b) => $a + $b, 0),
        new ProcIReduceField('product', '[c', fn($a, $b) => $a * $b, 1),
    ],
    ['[a', '[b'], // group by $row['a'] and $row['b']
)->toArray();
```

### `allMeets($callback): bool`

`true` if `$callback($item, $key)` is truthy for **all** elements (or the collection is empty).

### `anyMeets($callback): bool`

`true` if `$callback($item, $key)` is truthy for **at least one** element.

### `allAre(mixed $value, bool $exact = false): bool`

`true` if every element equals `$value` (`===` when `$exact` is `true`, `==` otherwise).

### `anyIs(mixed $value, bool $exact = false): bool`

`true` if at least one element equals `$value` (`===` when `$exact` is `true`, `==` otherwise).

### `deepMap(int $level, $callback): $this`

Applies `$callback($value, $keysPath)` to the elements found at nesting depth `$level` of a
(possibly multi-dimensional) array structure, replacing them in place.

### `iterSrc(): $this`

Normalizes the internal source into a plain array by iterating it once (useful after operations
that leave a non-array iterable as the source).

### `static selectFields($src, array $fields, bool $trans = false): array`

Picks a subset of keys out of `$src`. If `$trans` is `false`, `$fields` is a plain list of keys to
keep. If `$trans` is `true`, `$fields` is a `[oldKey => newKey]` map and the result is re-keyed
accordingly.

```php
ProcI::selectFields(['a' => 1, 'b' => 2, 'c' => 3], ['a', 'b']);
// ['a' => 1, 'b' => 2]
```

### `static cartesianProduct(array $arrays, $callback = null): array`

Returns the cartesian product of the given arrays (array of tuples), or, if `$callback` is given,
maps each tuple through `$callback(...$tuple)`.

```php
ProcI::cartesianProduct([[1, 2], ['a', 'b']]);
// [[1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']]
```

### `static cbValue($value)`

Returns a closure that always returns `$value`, ignoring its arguments — useful as a constant
callback.

### `static firstKey($arr)`

Returns the first key of a plain array (`reset()` + `key()`).

See also: [ProcIReduceField](ProcIReduceField.md), [CollectionDiff](CollectionDiff.md) and
[TreeMaker](TreeMaker.md), which are built on top of `ProcI`.
