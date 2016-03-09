<?php

function parseArgs() {
    global $argv;
    $val = array_merge($argv);
    $options = [];
    $content = [];
    array_shift($val);
    foreach($val as $part) {
        if (strpos($part, '--') === 0) {
            $options[] = substr($part, 2);
        } else if (strpos($part, '-') === 0) {
            $options = array_merge($options, str_split(substr($part, 1)));
        } else {
            $content[] = $part;
        }
    }
    $content = trim(str_replace(['"', "'"], '', implode(' ', $content)));
    return ['content' => $content, 'options' => $options];
}

function isId($val) {
    return ctype_digit($val);
}
function reply($message, $code=false) {
    if ($code) {
        $message = '```'.$message.'```';
    }
    echo $message;
    exit();
}
