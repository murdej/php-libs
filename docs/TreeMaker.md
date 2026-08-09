# TreeMaker

`Murdej\TreeMaker`

Builds a tree of [`TreeItem`](TreeItem.md) nodes out of a flat list of elements that reference
their parent by id (e.g. rows from a `categories` table with a `parent_id` column).

API reference: [`Murdej-TreeMaker`](../api-docs/api/classes/Murdej-TreeMaker.html)

## Methods

### `static make(array $elements, callable|string $getParentId, callable|string $getId, mixed $parentId = null): TreeItem[]`

Builds the tree. `$getParentId` and `$getId` extract the parent-id/id from an element (accepts
anything [`ProcI::prepareCallback()`](ProcI.md#static-preparecallbackcallback) understands, e.g. a
[`ProcI` string callback](ProcI.md#string-callbacks) like `'.parentId'`). `$parentId` is the id
whose children form the returned root level (`null` by default, for top-level elements).

```php
$rows = [
    ['id' => 1, 'parentId' => null, 'name' => 'Root'],
    ['id' => 2, 'parentId' => 1, 'name' => 'Child A'],
    ['id' => 3, 'parentId' => 1, 'name' => 'Child B'],
];

$tree = TreeMaker::make($rows, '[parentId', '[id');
// $tree[0]->item === $rows[0]; $tree[0]->children[0]->item === $rows[1]; ...
```

### `static linearize(array|TreeItem $items): TreeItem[]`

Flattens a tree (or a single node) back into a depth-first, flat `TreeItem[]` list, updating each
node's `$level` along the way. Useful for rendering an indented list from a tree.

### `static findFirst(array|TreeItem $items, callable $predicate): ?TreeItem`

Depth-first search for the first `TreeItem` in `$items` (or its descendants) for which
`$predicate($treeItem)` returns `true`. Returns `null` if none match.

### `static path(TreeItem $node): TreeItem[]`

Returns the list of `TreeItem`s from the root down to (and including) `$node`, by following
`$node->parent` links.

See also: [TreeItem](TreeItem.md), [ProcI](ProcI.md).
