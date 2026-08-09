# ErrorListMessage

`Murdej\DTO\ErrorListMessage extends SmartDTO`

A single validation error: a `field` name paired with a human-readable `message`. Instances are
collected in [`ErrorList::$messages`](ErrorList.md).

API reference: [`Murdej-DTO-ErrorListMessage`](../api-docs/api/classes/Murdej-DTO-ErrorListMessage.html)

## Constructor

```php
public function __construct(
    public string $field,
    public string $message,
)
```

See also: [ErrorList](ErrorList.md), [SmartDTO](SmartDTO.md).
