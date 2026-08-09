# Errors

`Murdej\Errors`

A tiny helper to turn PHP errors/warnings/notices into exceptions.

API reference: [`Murdej-Errors`](../api-docs/api/classes/Murdej-Errors.html)

## Methods

### `static toException(): void`

Installs a global error handler (via `set_error_handler()`) that throws a `\Exception` (with the
error message as the exception message and the error number as the code) for every triggered PHP
error, instead of the default warning/notice behavior.

```php
Errors::toException();

// From now on, e.g. an undefined array key access or division by zero warning
// will throw a \Exception instead of just emitting a warning.
try {
    $x = 1 / 0;
} catch (\Exception $e) {
    // handle it
}
```
