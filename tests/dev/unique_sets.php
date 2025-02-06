<?php

use Murdej\ProcI;

require_once __DIR__ . '/../../vendor/autoload.php';

$sets = [
	[ 1, 2, 3],
	[ 3, 2, 1],
	[ 1, 2],
];
print_r(
	ProcI::from($sets)
		->map(function($item) { sort($item); return $item; })
		->struct(fn($item) => serialize($item))
		->values()
		->toArray()
);