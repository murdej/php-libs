# Arrays

`Murdej\Arrays`

Static helper methods for working with plain and nested (multi-dimensional) arrays.

API reference: [`Murdej-Arrays`](../api-docs/api/classes/Murdej-Arrays.html)

## Methods

### `addDefaultsRecursive(array $src, array $defaults): array`

Recursively fills in missing keys in `$src` with values from `$defaults`. If both the source and
default value for a key are arrays, they are merged recursively (instead of being overwritten).

```php
Arrays::addDefaultsRecursive(
    ['a' => 1, 'nested' => ['x' => 1]],
    ['a' => 0, 'b' => 2, 'nested' => ['x' => 0, 'y' => 2]],
);
// ['a' => 1, 'nested' => ['x' => 1, 'y' => 2], 'b' => 2]
```

### `setValue(array &$array, array $path, mixed $value): void`

Sets a value inside a nested array by a path (array of keys). Missing intermediate keys are
created as empty arrays automatically. Throws `\Exception` if an intermediate node in the path
already exists but is not an array.

```php
$data = [];
Arrays::setValue($data, ['user', 'address', 'city'], 'Prague');
// $data === ['user' => ['address' => ['city' => 'Prague']]]
```

### `getValue(array $array, array $path, mixed $defaultValue = null): mixed`

Reads a value from a nested array by a path (array of keys). Returns `$defaultValue` if the path
does not exist. Throws `\Exception` if an intermediate node in the path is not an array.

```php
Arrays::getValue(['user' => ['address' => ['city' => 'Prague']]], ['user', 'address', 'city']);
// 'Prague'
Arrays::getValue(['user' => []], ['user', 'address', 'city'], 'unknown');
// 'unknown'
```

### `flatten(array $array, string $prefix = '', string $separator = '.', bool $firstLevel = true): array`

Flattens a nested array into a single-level array whose keys are the joined paths
(e.g. `parent.child`).

```php
Arrays::flatten(['a' => 1, 'b' => ['c' => 2, 'd' => 3]]);
// ['a' => 1, 'b.c' => 2, 'b.d' => 3]
```

### `unflatten(array $array, string $prefix = '', string $separator = '.'): array`

The inverse of `flatten()` — turns dot-separated keys back into a nested array structure. When
`$prefix` is given, only keys starting with that prefix are used (and the prefix is stripped).

```php
Arrays::unflatten(['a' => 1, 'b.c' => 2, 'b.d' => 3]);
// ['a' => 1, 'b' => ['c' => 2, 'd' => 3]]
```

### `renameKeys(array $arr, array $keys): array`

Renames keys in `$arr` according to the `[$oldKey => $newKey]` map in `$keys`. Keys not listed in
`$keys` are left unchanged.

```php
Arrays::renameKeys(['first_name' => 'Jan', 'age' => 30], ['first_name' => 'firstName']);
// ['age' => 30, 'firstName' => 'Jan']
```
