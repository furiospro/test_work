<?php
$res = '';
$res_arr = [];

// Исхожу из того, что массив имеет строгую структуру: набор объектов по типу записей из базы данных
$arr = [
    [
        "internal_history_id"  => "2230893",
        "external_id"  => "8615",
        "external_commission"  =>"0.0005"
    ],
    [
        "internal_history_id" => "2230891",
        "external_id" => "2159",
        "external_commission" => "0.0200"
    ],
    [
        "internal_history_id" => "2230892",
        "external_id" => "5349",
        "external_commission" => "0.0060"
    ],
    [
        "internal_history_id" => "563089",
        "external_id" => "8659",
        "external_commission" => "0.0054"
    ]
];

$keyNameArr = array_keys($arr[0]);

foreach ($keyNameArr as $key) {
    $res_arr[$key] = array_column($arr, $key);
}

for ($j = 0; $j < count($keyNameArr); $j++) {
    $res .= sprintf("| %-30s", $keyNameArr[$j]);
}

$res .= PHP_EOL;

array_map(function () use (&$res) {
    $args = func_get_args();
    for ($n = 0; $n < count($args); $n++) {
        $res .= sprintf("| %-30s", $args[$n]);
    }
    $res .= PHP_EOL;
}, ...array_values($res_arr));

echo $res;