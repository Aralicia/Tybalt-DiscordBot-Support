<?php

$args = json_decode($argv[1]);

$data = (object)[
    'status' => 'ok',
    'print' => print_r($args, true)
];

echo json_encode($data);