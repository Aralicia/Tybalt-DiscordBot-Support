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
if (isId($pattern)) {
    $id = $pattern;
} else if (ChatLink::check($pattern)){
    $chatlink = new ChatLink($pattern);
    if ($chatlink->getHeader() != 'Trait') reply('This is not a Trait !');

    $id = $chatlink->getId();
} else {
    $failure = null;
    $id = getIdByName($pattern, 'trait', $failure);
    if ($id == FALSE) {
        if (is_array($failure)) {
            reply("I've found the following traits : `".implode('`, `', $failure)."`. Which one do you want ?");
        } else if ($failure < 1) {
            reply("I've not found anything, sorry.");
        } else {
            reply("I've found ".$failure." matches. Can you be more precise ?");
        }
    }
}

$data = api()->v2('traits', ['params' => ['id' => $id]]);

if ($data == null) {
    reply("I dont know this trait, sorry.");
}

include(__DIR__.'/templates/trait.php');
