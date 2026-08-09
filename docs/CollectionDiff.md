# CollectionDiff

`Murdej\CollectionDiff<TO, TN, TK>`

Compares an "old" and a "new" collection (e.g. records currently in the database vs. records
coming from a form/API) and splits the result into new, updated and deleted items, keyed by a
business key extracted from each item.

API reference: [`Murdej-CollectionDiff`](../api-docs/api/classes/Murdej-CollectionDiff.html)

## Properties

| Property | Type | Description |
|---|---|---|
| `$newItems` | `CollectionDiffItem<null, TN, TK>[]` | Items present only in the new collection. |
| `$newItemKeys` | `TK[]` | Keys of `$newItems`. |
| `$updatedItems` | `CollectionDiffItem<TO, TN, TK>[]` | Items present in both collections where `$diffCallback` reported a difference. |
| `$updatedItemKeys` | `TK[]` | Keys of `$updatedItems`. |
| `$deletedItems` | `CollectionDiffItem<TO, null, TK>[]` | Items present only in the old collection. |
| `$deletedItemKeys` | `TK[]` | Keys of `$deletedItems`. |

## Methods

### `static compare(iterable $oldCollection, callable|string $dbKeyField, iterable $newCollection, callable|string $newKeyField, callable $diffCallback): self`

Builds a `CollectionDiff` instance from two collections.

- `$oldCollection` / `$newCollection` — the two collections to compare.
- `$dbKeyField` / `$newKeyField` — how to extract the business key from an old/new item
  respectively. Accepts anything [`ProcI::prepareCallback()`](ProcI.md#static-preparecallbackcallback)
  understands, e.g. a plain `callable` or a [`ProcI` string callback](ProcI.md#string-callbacks)
  such as `'.id'`.
- `$diffCallback(mixed $oldItem, mixed $newItem): bool` — called for every item present in both
  collections (matched by key); return `true` if the item should be considered changed.

```php
$diff = CollectionDiff::compare(
    $dbRows,   '.id',
    $formRows, '.id',
    fn($old, $new) => $old->name !== $new->name,
);

foreach ($diff->newItems as $item) {
    // $item->newValue is a new row, insert it
}
foreach ($diff->updatedItems as $item) {
    // $item->oldValue / $item->newValue, update the row
}
foreach ($diff->deletedItems as $item) {
    // $item->oldValue, delete the row
}
```

See also: [CollectionDiffItem](CollectionDiffItem.md), [ProcI](ProcI.md).
