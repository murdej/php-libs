# ListResult

`Murdej\DTO\ListResult<T> extends SmartDTO`

A generic paginated list result: an `items` array plus pagination metadata (`totalCount`,
`limitFrom`, `limitCount`).

API reference: [`Murdej-DTO-ListResult`](../api-docs/api/classes/Murdej-DTO-ListResult.html)

## Constructor

```php
public function __construct(
    public array $items,
    public ?int $totalCount = null,
    public int $limitFrom = 0,
    public ?int $limitCount = null,
)
```

- `$items` — the `T[]` page of results.
- `$totalCount` — total number of items across all pages, if known.
- `$limitFrom` — offset of this page (number of items skipped).
- `$limitCount` — page size (number of items requested for this page).

## Methods

### `getIsLast(): bool`

`true` if this page reaches (or exceeds) `$totalCount`, i.e. `$limitFrom + $limitCount >= $totalCount`.

### `getNextLimitFrom(): int`

Returns the `limitFrom` value to use for the next page: `$limitFrom + $limitCount`.

### `mapItems($callback): void`

Replaces `$items` with the result of mapping each item through `$callback`, via
[`ProcI::map()`](ProcI.md#mapitemcallback-keycallback-null-this).

```php
$page = new ListResult(items: $rows, totalCount: 120, limitFrom: 0, limitCount: 20);
$page->mapItems(fn($row) => UserDto::fromArray($row));

if (!$page->getIsLast()) {
    $nextFrom = $page->getNextLimitFrom(); // 20
}
```

See also: [SmartDTO](SmartDTO.md), [ProcI](ProcI.md).
