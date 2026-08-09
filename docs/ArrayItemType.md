# ArrayItemType

`Murdej\DTO\Attributes\ArrayItemType`

A property attribute (`#[Attribute(Attribute::TARGET_PROPERTY)]`) used to tell
[`SmartDTO::fromArray()`](SmartDTO.md#static-fromarrayarray-data-object) which DTO class the
elements of an `array`-typed property should be converted to.

API reference: [`Murdej-DTO-Attributes-ArrayItemType`](../api-docs/api/classes/Murdej-DTO-Attributes-ArrayItemType.html)

## Constructor

```php
public function __construct(
    public string $itemType, // class-string of the item DTO
)
```

## Usage

```php
class Order extends SmartDTO {
    public function __construct(
        #[ArrayItemType(OrderLine::class)]
        public array $lines = [],
    ) {}
}
```

When `Order::fromArray($data)` runs, each element of `$data['lines']` is converted via
`OrderLine::fromArray($element)` instead of being copied as-is.

