# CollectionDiffItem

`Murdej\CollectionDiffItem<TO, TN, TK>`

A simple value object produced by [`CollectionDiff::compare()`](CollectionDiff.md), holding the
old value, the new value and the shared key for one diffed item.

API reference: [`Murdej-CollectionDiffItem`](../api-docs/api/classes/Murdej-CollectionDiffItem.html)

## Constructor

```php
public function __construct(
    public mixed $oldValue,
    public mixed $newValue,
    public mixed $key,
)
```

- `$oldValue` — the item from the old collection, or `null` for newly added items.
- `$newValue` — the item from the new collection, or `null` for deleted items.
- `$key` — the business key shared by both items (as extracted by `CollectionDiff::compare()`).

See also: [CollectionDiff](CollectionDiff.md).
