# PHP libs by Murdej

A collection of small, dependency-free PHP utility classes: array/collection helpers, a fluent
collection-processing pipeline (`ProcI`), DTO base classes, simple validation, math helpers, a
string-scanning helper and a tree builder.

Each class below has its own documentation page. Every page also links to the corresponding
auto-generated [phpDocumentor](../api-docs/api/index.html) API reference, which is generated
from the source's docblocks (run `phpDocumentor.phar` to (re)build it).

## Namespace `Murdej`

| Class | Description |
|---|---|
| [Arrays](Arrays.md) | Static helpers for working with (nested) arrays: defaults, get/set by path, flatten/unflatten, key renaming. |
| [CollectionDiff](CollectionDiff.md) | Compares two collections (e.g. old/new state) and produces new/updated/deleted item sets. |
| [CollectionDiffItem](CollectionDiffItem.md) | Value object holding one old/new/key triple produced by `CollectionDiff`. |
| [ErrorList](ErrorList.md) | Collects field validation errors (`checkFill`, `checkPreg`, `checkEmail`, ...). |
| [Errors](Errors.md) | Converts PHP errors/warnings into exceptions. |
| [Math](Math.md) | Small numeric helpers: rate-based division, tolerant comparison, float parsing. |
| [ProcI](ProcI.md) | Fluent, chainable processor for arrays/iterables (map, filter, order, group, reduce, ...). |
| [ProcIReduceField](ProcIReduceField.md) | Field definition used by `ProcI::reduceBy()`. |
| [StringWalk](StringWalk.md) | Stateful cursor for scanning/parsing a string (find, mark, substr). |
| [TreeItem](TreeItem.md) | A single node of a tree built by `TreeMaker`. |
| [TreeMaker](TreeMaker.md) | Builds a tree (`TreeItem[]`) out of a flat list of elements with parent references. |

## Namespace `Murdej\DTO`

| Class | Description |
|---|---|
| [SmartDTO](SmartDTO.md) | Abstract DTO base class: array/JSON (de)serialization, enums, `DateTime`, nested DTOs, DB (de)serialization. |
| [CommonResult](CommonResult.md) | Generic "operation result" DTO combining payload data with an `ErrorList`. |
| [ErrorListMessage](ErrorListMessage.md) | A single `field` + `message` validation error, as used by `ErrorList`. |
| [ListResult](ListResult.md) | Generic paginated list result DTO (`items`, `totalCount`, `limitFrom`, `limitCount`). |

## Attributes

| Class | Description |
|---|---|
| [ArrayItemType](ArrayItemType.md) | Property attribute that tells `SmartDTO::fromArray()` which DTO class to use for the items of an `array` property. |
