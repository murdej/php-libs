<?php

use Murdej\ProcI;

require_once __DIR__ . '/../../vendor/autoload.php';

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

print_r(
    ProcI::cartesianProduct(
        [
            [ 1, 2, 3 ],
            [ 'a', 'b', 'c' ]
        ],
        fn($a, $b) => [ 'a' => $a, 'b' => $b ]
    )
);