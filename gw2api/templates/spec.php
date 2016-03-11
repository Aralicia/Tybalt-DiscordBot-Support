<?php
require_once(dirname(__DIR__).'/inc/gw2_facts.inc.php');
require_once(dirname(__DIR__).'/inc/gw2_chatlink.inc.php');

function getTier($id) {
    return ($id == 1 ? "Adept" : ($id == 2 ? "Master" : ($id == 3 ? "Grandmaster" : "")));
}
function getTraitTitle($trait, $verbose) {
    return $trait->name.' • '.getNameById($trait->specialization, 'specialization').' • '.$trait->slot.' '.getTier($trait->tier).' trait';
}
function getSpecType($specType){
    return ($specType == 1 ? "Elite" : "Core");
}
function getSpecializationTitle($spec, $verbose) {
    /*
    $desc = 'Core specialization';
    if($spec->elite){
        $desc = 'Elite specialization';
    }
    */
    return $spec->name.' • '.$spec->profession.' • '.getSpecType($spec->elite).' specialization';
}
function getSpecTraits($specData) {
    $traitData = api()->v2('traits', ['params' => ['ids' => array_merge($specData->minor_traits, $specData->major_traits)]]);
    $traits = (object)[
        'adept' => (object)['minor' => null, 'major' => []],
        'master' => (object)['minor' => null, 'major' => []],
        'grandmaster' => (object)['minor' => null, 'major' => []]
    ];
    $tiers = [$traits->adept, $traits->master, $traits->grandmaster];
    foreach($traitData as $trait) {
        $tier = $tiers[$trait->tier];
        if ($trait->slot == 'Minor') {
            $tiers[$trait->tier]->minor = $trait;
        } else {
            $tiers[$trait->tier]->major[$trait->order] = $trait;
        }
    }
    return $traits;
}





$traits = getSpecTraits($data);

$verbose = (in_array('v', $options) || in_array('verbose', $options));
$debug = (in_array('d', $options) || in_array('debug', $options));
if ($debug) {
    reply(print_r($data));
}
$lines = [];
//$lines[] = getTraitTitle($data, $verbose);
$lines[] = getSpecializationTitle($data, $verbose);
$lines[] = $traits;
reply(implode("\r\n", array_filter($lines)), true);
