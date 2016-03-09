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

if (empty($pattern)) reply('Yes, I have skills, thank you.');

$id = '';
if (isId($pattern)) {
    $id = $pattern;
} else if (ChatLink::check($pattern)){
    $chatlink = new ChatLink($pattern);
    if ($chatlink->getHeader() != 'Skill') reply('This is not a Skill !');

    $id = $chatlink->getId();
} else {
    $failure = null;
    $id = getIdByName($pattern, 'skill', $failure);
    if ($id == FALSE) {
        if (is_array($failure)) {
            reply("I've found the following skills : `".implode('`, `', $failure)."`. Which one do you want ?");
        } else if ($failure < 1) {
            reply("I've not found anything, sorry.");
        } else {
            reply("I've found ".$failure." matches. Can you be more precise ?");
        }
    }
}

$data = api()->v2('skills', ['params' => ['id' => $id]]);

if ($data == null) {
    reply("I dont know this skill, sorry.");
}

include(__DIR__.'/templates/skill.php');
