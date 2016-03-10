<?php

require_once(dirname(__DIR__).'/inc/gw2_facts.inc.php');
require_once(dirname(__DIR__).'/inc/gw2_chatlink.inc.php');

function getTier($id) {
    return ($id == 1 ? "Adept" : ($id == 2 ? "Master" : ($id == 3 ? "Grandmaster" : "")));
}
function getTraitTitle($trait, $verbose) {
    return $trait->name.' - '.getNameById($trait->specialization, 'specialization').' '.$trait->slot.' '.getTier($trait->tier).' trait';
}
$verbose = (in_array('v', $options) || in_array('verbose', $options));
$debug = (in_array('d', $options) || in_array('debug', $options));

if ($debug) {
    reply(print_r($data));
}

$lines = [];
$lines[] = getTraitTitle($data, $verbose);
$lines[] = $data->description;
reply(implode("\r\n", array_filter($lines)), true);
