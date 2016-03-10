<?php

function parseArgs() {
    global $argv;
    $val = array_merge($argv);
    $options = [];
    $splits = [];
    array_shift($val);
    foreach($val as $part) {
        if (strpos($part, '--') === 0) {
            $options[] = substr($part, 2);
        } else if (strpos($part, '-') === 0) {
            $options = array_merge($options, str_split(substr($part, 1)));
        } else {
            $splits[] = $part;
        }
    }
    $content = trim(str_replace(['"', "'"], '', implode(' ', $splits)));
    return ['content' => $content, 'options' => $options, $splits];
}

function isId($val) {
    return ctype_digit($val);
}
function reply($message, $code=false) {
    if ($code) {
        $message = '```'."\r\n".$message.'```';
    }
    echo $message;
    exit();
}

function utf($code) {
    return html_entity_decode('&#'.$code.';');
}
