<?php

use Murdej\Arrays;
use Murdej\ProcI;

require_once __DIR__ . '/../../vendor/autoload.php';

$data = [
    "zeroRecords" => "None",
    "searchBuilder" => [
    "add" => "Add condition",
        "conditions" => [
        "date" => [
            "after" => "po"
            ]
        ]
    ]
];

$dataFlat = Arrays::flatten($data, 'Prefix_');
$dataFlat['foo'] = 'To removed';
$dataStruct = Arrays::unflatten($dataFlat, 'Prefix_');
print_r($dataStruct);
print_r($dataFlat);

$dataFlat = Arrays::flatten($data, separator: '__');
$dataFlat['foo'] = 'To removed';
$dataStruct = Arrays::unflatten($dataFlat, separator: '__');
print_r($dataStruct);
print_r($dataFlat);

