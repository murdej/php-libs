# Math

`Murdej\Math`

Small collection of numeric helper functions.

API reference: [`Murdej-Math`](../api-docs/api/classes/Murdej-Math.html)

## Methods

### `static divideByRate(int|float $fullValue, array $rates): array`

Splits `$fullValue` proportionally according to `$rates` (preserving the array's keys), so that
the resulting values sum up to `$fullValue`. Throws `\Exception` if the sum of `$rates` is zero.

```php
Math::divideByRate(100, [1, 1, 2]);
// [25, 25, 50]
```

### `static compareAccuracy(mixed $a, mixed $b, float $accuracy = 1): int`

Compares two values after normalizing/rounding them by `$accuracy` (both values are divided by
`$accuracy` and rounded before comparison). A larger `$accuracy` means a coarser ("less accurate")
comparison. Returns `-1`, `0` or `1`, like the spaceship operator.

```php
Math::compareAccuracy(10.01, 10.02, 0.1); // 0  (both round to 100)
Math::compareAccuracy(10.01, 10.2, 0.1);  // -1
```

### `static parseFloat(mixed $value): ?float`

Tolerantly parses a float from a mixed value (int, float, or string). For strings, it trims
whitespace and accepts both `.` and `,` as the decimal separator. Returns `null` for `null`,
empty strings, or non-numeric strings.

```php
Math::parseFloat(' 12,5 '); // 12.5
Math::parseFloat('12.5');   // 12.5
Math::parseFloat('');       // null
Math::parseFloat('abc');    // null
```
