<?php

use Murdej\ProcI;

require_once '../../vendor/autoload.php';

$data = [
    (object)[
        'a' => [
            'b' => 42,
        ]
    ],
    (object)[
        'a' => [
            'b' => '422',
        ]
    ],
];

print_r(
    ProcI::from($data)
        ->map('.a[b')
        ->toArray()
);