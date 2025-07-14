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
echo "Callbacks:\n";
foreach (
	[
		'.a',
		'(int).a',
		'(float).a',
		'-.a',
		'-(int).a',
		'-(float).a',
		'.b.c',
		'.d[c',
	] as $callback
) {
	echo "$callback = ";
	var_dump(
		ProcI::from([
			(object)[
				'a' => '1.23',
				'b' => (object)['c' => 123],
				'd' => ['c' => 456],
			]
		])
			->map($callback)
			->first()
	);
	echo "\n";
}

$data = [
	(object)[
		'a' => 'a',
		'b' => 42,
	],
	(object)[
		'a' => 'b',
		'b' => 100,
	],
	(object)[
		'a' => 'b',
		'b' => 200,
	],
];

echo "Unique:\n";
print_r(
	ProcI::from($data)
		->unique(
			'.a',
		)
		->toArray()
);
echo "Unique:\n";
print_r(
	ProcI::from($data)
		->unique(
			'.a',
			fn($items) => ProcI::from($items)->orderBy('-.b')->first()
		)
		->toArray()
);
