<?php

use Murdej\Errors;

require_once __DIR__ . '/../../src/Errors.php';

Errors::toException();

foreach([
    fn() => include 'kkk',
    fn() => foo(),
    fn() => 10 / 0,
    fn() => 10000000000000 * 100000000000,
    fn() => $gg,
    fn() => new Prase(),
    fn() => fopen('dddd', 'r'),
] as $callback) {
    try {
        $callback();
    } catch (Throwable $e) {
        echo "Catched " . $e . "\n\n";
    }
}
echo "\nDone\n";