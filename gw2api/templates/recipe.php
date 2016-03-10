<?php

require_once(dirname(__DIR__).'/inc/gw2_facts.inc.php');
require_once(dirname(__DIR__).'/inc/gw2_chatlink.inc.php');

function getTitle($data, $verbose) {
    return getNameById($data->output_item_id, 'item').' - Recipe';
}

$verbose = (in_array('v', $options) || in_array('verbose', $options));
$debug = (in_array('d', $options) || in_array('debug', $options));

if ($debug) {
    reply(print_r($data));
}

$lines = [];
$lines[] = getTitle($data, $verbose);
//$lines[] = $data->description;
reply(implode("\r\n", array_filter($lines)), true);
