<?php 

require_once(__DIR__.'/inc/common.inc.php');
require_once(__DIR__.'/inc/database.inc.php');
require_once(__DIR__.'/inc/api.inc.php');
require_once(__DIR__.'/inc/gw2_facts.inc.php');
require_once(__DIR__.'/inc/gw2_chatlink.inc.php');

$args = parseArgs();
$options = $args['options'];
$pattern = $args['content'];
$name = "";
$raw = "";

if (empty($pattern)) reply('I\'m sorry ?');
$id = '';

//$spec = [];
//$spec = implode(' ', $pattern);

$spec = $args['splits'];

//Have to check this
if (isId($pattern)) {
    $id = $pattern;
/*} else if (ChatLink::check($pattern)){
    $chatlink = new ChatLink($pattern);
    if ($chatlink->getHeader() != 'Specialization') reply('This is not a Specialization !');
    $id = $chatlink->getId();*/
} else {
    $failure = null;
    $id = getIdByName($pattern, 'specialization', $failure);
    if ($id == FALSE) {
        if (is_array($failure)) {
            reply("I've found the following specializations : `".implode('`, `', $failure)."`. Which one do you want ?");
        } else if ($failure < 1) {
            reply("I've not found anything, sorry.");
        } else {
            reply("I've found ".$failure." matches. Can you be more precise ?");
        }
    }
}
/*
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
    return $traits
}

$specData = api()->v2('specializations', ['params' => ['id' => $id]]);
$traits = getSpecTraits($specData);

$mytrait = $traits->master->minor;
$mytrait = $traits->adept->major[2];
*/

$data = api()->v2('specializations', ['params' => ['id' => $id]]);
if ($data == null) {
    reply("I dont know this specialization, sorry.");
}

include(__DIR__.'/templates/spec.php');
