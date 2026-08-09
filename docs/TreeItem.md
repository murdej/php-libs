# TreeItem

`Murdej\TreeItem<T>`

A single node of a tree, as built and returned by [`TreeMaker`](TreeMaker.md).

API reference: [`Murdej-TreeItem`](../api-docs/api/classes/Murdej-TreeItem.html)

## Constructor

```php
public function __construct(
    public mixed $item,
    public array $children = [],
    public int $level = 0,
    public ?TreeItem $parent = null,
)
```

- `$item` — the wrapped original element (`T`).
- `$children` — child `TreeItem<T>[]` nodes.
- `$level` — nesting depth (`0` for roots); set/updated by `TreeMaker::linearize()`.
- `$parent` — the parent node, or `null` for a root node.

See also: [TreeMaker](TreeMaker.md).
