<?php

namespace Murdej\DTO;

use App\Contracts\Attributes\ArrayItemType;
use BackedEnum;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

abstract class SmartDTO implements JsonSerializable, \ArrayAccess
{

	public function offsetExists(mixed $offset): bool
	{
		return isset($this->$offset);
	}

	public function offsetGet(mixed $offset): mixed
	{
		return $this->$offset;
	}

	public function offsetSet(mixed $offset, mixed $value): void
	{
		$this->$offset = $value;
	}

	public function offsetUnset(mixed $offset): void
	{
		unset($this->$offset);
	}

	public function toArray(): array
	{
		return array_map(
			fn($value) => $this->valueTo($value),
			get_object_vars($this),
		);
	}


	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}

	protected function valueTo(mixed $value): mixed
	{
		if ($value === null) return null;

		if ($value instanceof DateTimeInterface) {
			return DateTime::createFromInterface($value)->format(DATE_ATOM);
		}

		if ($value instanceof self) {
			return $value->toArray();
		}

		if (is_array($value)) {
			return array_map(fn ($item) => $this->valueTo($item), $value);
		}

		return $value;
	}

	public static function fromArray(array $data): object
    {
        $reflection = new ReflectionClass(get_called_class());
        $constructor = $reflection->getConstructor();
        $args = [];

        if ($constructor) {
            foreach ($constructor->getParameters() as $param) {
                $name = $param->getName();
                if (array_key_exists($name, $data)) {
                    $args[] = self::valueFrom($data[$name], $reflection->getProperty($name));
                    unset($data[$name]);
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    $args[] = null;
                }
            }
        }

        $instance = $reflection->newInstanceArgs($args);

        foreach ($data as $name => $value) {
            if ($reflection->hasProperty($name)) {
                $property = $reflection->getProperty($name);
                if ($property->isReadOnly() && $property->isInitialized($instance)) continue;
				if (!$property->isPublic()) continue;

                $convertedValue = self::valueFrom($value, $property);
                $property->setValue($instance, $convertedValue);
            }
        }

        return $instance;
    }

    /**
     * Pomocná metoda pro konverzi hodnoty na základě reflexe vlastnosti.
     * @param mixed $value Hodnota z pole.
     * @param ReflectionProperty $property Reflexe cílové vlastnosti.
     * @return mixed
     */
    private static function valueFrom(mixed $value, ReflectionProperty $property): mixed
    {
        $type = $property->getType();

        if (!$type instanceof ReflectionNamedType) {
            return $value;
        }

        $typeName = $type->getName();

        if (enum_exists($typeName)) {
            /** @var class-string<BackedEnum> $typeName */
            return $typeName::tryFrom($value) ?? $value;
        }

        if (is_subclass_of($typeName, DateTimeInterface::class)) {
            return self::parseDateTime($value, $typeName);
        }

		if (is_subclass_of($typeName, self::class) && $value !== null) {
			return $typeName::fromArray($value);
		}

        if ($typeName === 'array' && is_array($value)) {
            $attrs = $property->getAttributes(ArrayItemType::class);
            if (!empty($attrs)) {
                /** @var ArrayItemType $attr */
                $attr = $attrs[0]->newInstance();
                $itemClass = $attr->itemType;
                return array_map(fn($item) => $itemClass::fromArray($item), $value);
            }
        }

        return $value;
    }

	protected static function parseDateTime(mixed $value, string $targetClass): ?DateTimeInterface
	{
		if ($value === null || $value === '') {
			return null;
		}

		if ($value instanceof $targetClass) {
			return $value;
		}

		if ($value instanceof DateTimeInterface) {
			return new $targetClass($value);
		}

		if (is_string($value)) {
			return match ($targetClass) {
				DateTimeInterface::class => new DateTimeImmutable($value),
				default => new $targetClass($value),
			};
		}

		return null;
	}

	/**
	 * Serialize to DB
	 */
	public function toDbValue(): string
	{
		return json_encode($this->toArray());
	}

	/**
	 * Deserialize from DB
	 */
	public static function fromDbValue($value) {
        if (is_string($value)) {
            $value = $value ? json_decode($value, true) : null;
        }
		return get_called_class()::fromArray($value ?? []);
	}
}
