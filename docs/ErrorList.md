# ErrorList

`Murdej\ErrorList`

A small validation helper that collects field-level error messages
([`ErrorListMessage`](ErrorListMessage.md)) against a source data array/`ArrayAccess`.
Implements `JsonSerializable` (serializes to the list of messages).

API reference: [`Murdej-ErrorList`](../api-docs/api/classes/Murdej-ErrorList.html)

## Properties

| Property | Type | Description |
|---|---|---|
| `$src` | `array\|ArrayAccess` | The data being validated. |
| `$messages` | `ErrorListMessage[]` | Collected error messages. |
| `$pathSeparator` | `string` | Separator used to address nested fields in `$src` (default `/`). |

## Constructor

```php
public function __construct(public array|ArrayAccess $src = [])
```

## Methods

### `checkFill(string|array $field, string $message = "..."): void`

Adds an error if the value at `$field` (read via `getSrcValue()`) is empty/falsy. `$field` can
also be an array of `field => message` pairs (or `message => field` when the array is a plain
list) to check several fields at once.

```php
$errors = new ErrorList($request->all());
$errors->checkFill('email', 'Email is required');
$errors->checkFill(['name' => 'Name is required', 'email' => 'Email is required']);
```

### `checkPreg(string|array $field, string $regex, string $message = "..."): void`

Adds an error if the value at `$field` does not match the given regular expression. Accepts the
same "single field" / "array of fields" forms as `checkFill()`.

### `checkEmail(string|array $field, string $message = "..."): void`

Shortcut for `checkPreg()` with a built-in email-format regular expression.

### `addError(string $field, string $message): ErrorListMessage`

Adds an error message directly and returns the created `ErrorListMessage`.

### `getSrcValue(string $path): mixed`

Reads a (possibly nested) value from `$src` using `$pathSeparator`-separated path segments, e.g.
`'address/city'`. Throws `InvalidArgumentException` if an intermediate segment exists but is not
an array. Returns `null` if the path does not exist.

### `isValid(): bool`

Returns `true` if no errors have been collected (`count($this->messages) === 0`).

### `jsonSerialize(): mixed`

Returns `$this->messages` for JSON serialization.

> **Note:** the built-in default messages (`checkFill`, `checkPreg`) are written in Czech
> ("Pole musí být vyplněno", "Pole má chybný formát"). Pass an explicit `$message` if you need
> English (or any other language) text.

See also: [ErrorListMessage](ErrorListMessage.md), [CommonResult](CommonResult.md).
