# ProcIReduceField

`Murdej\ProcIReduceField`

Describes one aggregated output field for [`ProcI::reduceBy()`](ProcI.md#reducebyarray-fields-array-byfields-this).

API reference: [`Murdej-ProcIReduceField`](../api-docs/api/classes/Murdej-ProcIReduceField.html)

## Constructor

```php
public function __construct(
    public string $fieldName,
    public string|\Closure $getCallback,
    public string|\Closure $reduceCallback,
    public mixed $initValue,
)
```

- `$fieldName` — the key under which the aggregated value will appear in each output row.
- `$getCallback` — extracts the per-row value to aggregate; a [`ProcI` string callback](ProcI.md#string-callbacks)
  or a closure/callable.
- `$reduceCallback($carry, $value)` — combines the running aggregate (`$carry`, starting at
  `$initValue`) with the value extracted by `$getCallback` for the current row.
- `$initValue` — the initial value of the aggregate for each group.

```php
new ProcIReduceField('sum', '[amount', fn($carry, $v) => $carry + $v, 0);
```

See also: [ProcI::reduceBy()](ProcI.md#reducebyarray-fields-array-byfields-this).
