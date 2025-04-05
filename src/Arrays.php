<?php

namespace Murdej;

class Arrays {
    /**
     * Recursively fills in missing values in an input array based on a template array.
     *
     * This function iterates through the template array and checks if the corresponding
     * keys exist in the input array. If a key is missing, it is added to the input
     * array with the default value from the template. If both the input and template
     * values for a key are arrays, the function recursively calls itself to merge
     * the nested arrays.
     *
     * @param array $src The input array to be filled.
     * @param array $defaults The template array with default values.
     *
     * @return array The input array with missing values filled in.
     */
    public static function addDefaultsRecursive(array $src, array $defaults): array
    {
        foreach ($defaults as $klic => $hodnota) {
            if (!isset($src[$klic])) {
                $src[$klic] = $hodnota;
            } elseif (is_array($hodnota) && is_array($src[$klic])) {
                $src[$klic] = self::addDefaultsRecursive($src[$klic], $hodnota);
            }
        }
        return $src;
    }

    /**
     * Sets a value within a nested array using a specified path.
     * If intermediate keys do not exist, they will be created as empty arrays.
     *
     * @param array<mixed> &$array The array to modify (passed by reference).
     * @param (string|int)[] $path An array of keys representing the path to the value.
     * @param mixed $value The value to set at the specified path.
     * @return void
     * @throws \Exception If a node in the path (excluding the target) is not an array.
     */
    public static function setValue(array &$array, array $path, mixed $value) : void
    {
        $node = &$array;
        $i = 0;
        foreach ($path as $i => $key) {
            if ($i < count($path) - 1) {
                if (!is_array($node)) throw new \Exception(
                    "Target node '" . implode("/", array_slice($path, $i + 1)) . "' is not an array.");
                if (!array_key_exists($key, $node)) $node[$key] = [];
                $node = &$node[$key];
            } else {
                $node[$key] = $value;
            }
        }
    }

    /**
     * Retrieves a value from a nested array using a specified path.
     * If the path does not exist, the provided default value is returned.
     *
     * @param array<mixed> $array The array to search within.
     * @param (string|int)[] $path An array of keys representing the path to the value.
     * @param mixed $defaultValue The value to return if the path does not exist (defaults to null).
     * @return mixed The value at the specified path, or the default value if not found.
     * @throws \Exception If a node in the path (excluding the target) is not an array.
     */
    public static function getValue(array $array, array $path, mixed $defaultValue = null) : mixed
    {
        $node = $array;
        foreach ($path as $i => $key) {
            if ($i < count($path) - 1) {
                if (!is_array($node)) throw new \Exception(
                    "Target node '" . implode("/", array_slice($path, $i + 1)) . "' is not an array.");
                if (!array_key_exists($key, $node)) return $defaultValue;
                $node = $node[$key];
            } else {
                return array_key_exists($key, $node)
                    ? $node[$key]
                    : $defaultValue;
            }
        }
        return $defaultValue;
    }

    public static function flatten(array $array, string $prefix = '', string $separator = '.', bool $firstLevel = true) : array {
        $result = [];
        foreach ($array as $key => $value) {
            $new_key = $prefix . ($firstLevel ? '' : $separator) . $key;
            if (is_array($value)) {
                $result = array_merge($result, self::flatten($value, $new_key, $separator, false));
            } else {
                $result[$new_key] = $value;
            }
        }
        return $result;
    }

    public static function unflatten(array $array, string $prefix = '', string $separator = '.') {
        $result = [];
        foreach ($array as $key => $value) {
            if ($prefix) {
                if (!str_starts_with($key, $prefix)) continue;
                $key = substr($key, strlen($prefix));
            }
            $keys = explode($separator, $key);
            $temp = &$result;
            foreach ($keys as $inner_key) {
                if (!isset($temp[$inner_key])) {
                    $temp[$inner_key] = [];
                }
                $temp = &$temp[$inner_key];
            }
            $temp = $value;
        }
        return $result;
    }
}