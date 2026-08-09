# SmartDTO

`Murdej\DTO\SmartDTO`

Abstract base class for typed DTOs. Provides array/JSON (de)serialization with automatic
conversion of enums, `DateTimeInterface` properties, nested `SmartDTO` properties, and
`array`-typed properties whose item type is declared via the [`ArrayItemType`](ArrayItemType.md)
attribute. Implements `JsonSerializable` and `ArrayAccess`.

API reference: [`Murdej-DTO-SmartDTO`](../api-docs/api/classes/Murdej-DTO-SmartDTO.html)

Extended by [`CommonResult`](CommonResult.md), [`ErrorListMessage`](ErrorListMessage.md) and
[`ListResult`](ListResult.md).

## `ArrayAccess`

`SmartDTO` implements `ArrayAccess`, so instances can be read/written like arrays (`$dto['field']`),
which is a thin wrapper around the corresponding property.

## Methods

### `toArray(): array`

Converts the DTO (and, recursively, any nested `SmartDTO`/`DateTimeInterface`/array values) into
a plain array. `DateTimeInterface` values are formatted with `DATE_ATOM`.

### `jsonSerialize(): mixed`

Returns `toArray()`, so DTOs can be passed directly to `json_encode()`.

### `static fromArray(array $data): object`

Creates an instance from an associative array:

1. Constructor parameters are filled from matching keys in `$data` (converting values as needed —
   see below); missing keys use the parameter's default value, or `null` if none.
2. Any remaining keys in `$data` are assigned to matching **public**, non-readonly-already-initialized
   properties via reflection.

Per-property value conversion (based on the property's declared type):

| Declared type | Conversion |
|---|---|
| `enum` (backed) | `EnumType::tryFrom($value)` (falls back to the raw value if it doesn't match a case) |
| `DateTimeInterface` subtype | Parsed via `parseDateTime()` (accepts a matching instance, any `DateTimeInterface`, or a date/time string) |
| another `SmartDTO` subclass | `SubDTO::fromArray($value)` |
| `array`, with `#[ArrayItemType(ItemDto::class)]` on the property | each element is converted via `ItemDto::fromArray($item)` |
| anything else | used as-is |

```php
class Address extends SmartDTO {
    public function __construct(public string $city) {}
}

class User extends SmartDTO {
    public function __construct(
        public string $name,
        public Address $address,
        #[ArrayItemType(Address::class)]
        public array $otherAddresses = [],
    ) {}
}

$user = User::fromArray([
    'name' => 'Jan',
    'address' => ['city' => 'Prague'],
    'otherAddresses' => [['city' => 'Brno'], ['city' => 'Ostrava']],
]);
```

### `toDbValue(): string`

Serializes the DTO to a JSON string (`json_encode($this->toArray())`), suitable for storing in a
single database column.

### `static fromDbValue($value): static`

The inverse of `toDbValue()`: accepts either a JSON string or an already-decoded array (or `null`,
treated as `[]`) and builds an instance via `fromArray()`.

See also: [ArrayItemType](ArrayItemType.md).
