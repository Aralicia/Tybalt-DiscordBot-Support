<?php

require_once(dirname(__DIR__).'/inc/gw2_facts.inc.php');
require_once(dirname(__DIR__).'/inc/gw2_chatlink.inc.php');

function getTitle($skill, $verbose) {
    return $skill->name.' - '.$skill->slot.' trait';
}
$verbose = (in_array('v', $options) || in_array('verbose', $options));

$lines = [];
$lines[] = getTitle($data, $verbose);
$lines[] = $data->description;
reply(implode("\r\n", array_filter($lines)), true);
