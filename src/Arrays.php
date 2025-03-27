<?php

namespace Murdej;

use mysql_xdevapi\Exception;

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


    public static function setValue(array &$array, array $path, mixed $value) : void
    {
        $node = &$array;
        $i = 0;
        foreach ($path as $i => $key) {
            if ($i < count($path) - 1) {
                if (!is_array($node)) throw new Exception(
                    "Target node '" . implode("/", array_slice($path, $i + 1)) . "' is not an array.");
                if (!array_key_exists($key, $node)) $node[$key] = [];
                $node = &$node[$key];
            } else {
                $node[$key] = $value;
            }
        }
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