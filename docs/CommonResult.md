# CommonResult

`Murdej\DTO\CommonResult extends SmartDTO`

A generic "operation result" envelope combining arbitrary payload `$data` with an
[`ErrorList`](ErrorList.md), and exposing a computed `ok`/`error` status. Commonly used as the
return type / JSON body of API endpoints or service methods.

API reference: [`Murdej-DTO-CommonResult`](../api-docs/api/classes/Murdej-DTO-CommonResult.html)

## Constants

- `CommonResult::StatusOk = 'ok'`
- `CommonResult::StatusError = 'error'`

## Properties

| Property | Type | Description |
|---|---|---|
| `$data` | `mixed` | The result payload. |
| `$errors` | `ErrorList` | Validation/operation errors; initialized from `$data` in the constructor. |

## Constructor

```php
public function __construct(public mixed $data = [])
```

Initializes `$errors` as `new ErrorList($data ?? [])` (so error-checking helpers on `$errors` can
read from `$data` right away).

## Methods

### `getStatus(): string`

Returns `CommonResult::StatusOk` if `$errors->isValid()`, otherwise `CommonResult::StatusError`.

### `jsonSerialize(): mixed`

Serializes to `['errors' => ..., 'status' => ..., 'data' => ...]` (overrides `SmartDTO`'s default
`toArray()`-based serialization).

### `static error(ErrorList $errors): static`

Builds an "error" result carrying the given `ErrorList`.

### `static ok(mixed $data = null, mixed $defaultData = []): static`

Builds a "success" result with `$data` (or `$defaultData` if `$data` is `null`).

```php
$errors = new ErrorList($input);
$errors->checkFill('email');

$result = $errors->isValid()
    ? CommonResult::ok(['id' => 1])
    : CommonResult::error($errors);

echo json_encode($result);
```

See also: [ErrorList](ErrorList.md), [SmartDTO](SmartDTO.md).
