<?php

require_once(__DIR__.'/inc/common.inc.php');
require_once(__DIR__.'/inc/database.inc.php');
require_once(__DIR__.'/inc/api.inc.php');
require_once(__DIR__.'/inc/gw2_facts.inc.php');
require_once(__DIR__.'/inc/gw2_chatlink.inc.php');

function displayCoins($coins) {
    if ($coins['gp'] > 0) {
        return $coins['gp'].'gp '.$coins['sp'].'sp '.$coins['cp'].'cp';
    } else if ($coins['sp'] > 0) {
        return $coins['sp'].'sp '.$coins['cp'].'cp';
    }
    return $coins['cp'].'cp';
}
function fetchAndDisplay($id, $entrypoint, $template, $options, $error) {
    $data = api()->v2($entrypoint, ['params' => ['id' => $id]]);
    if ($data == null) {
        reply($error);
    }
    include(__DIR__.'/templates/'.$template.'.php');
}
function displayItem($link) {
  $data = $link->getItemData();
  $message = "This is an Item with id ".$data['item_id'];
  if (isset($data['wardrobe_id'])) {
    $message .= " with skin ".$data['wardrobe_id'];
  }
  if (isset($data['sigils'])) {
    $message .= " containing sigils ".implode(',', $data['sigils']);
  }
  reply($message);
}

$args = parseArgs();
$options = $args['options'];
$code = $args['content'];
$name = "";
$raw = "";

if (!ChatLink::check($code)) {
    reply('This is not a Chat Link');
}
$link = new ChatLink($code);
switch($link->getHeader()) {
    case 'Coin':
        reply("Coins : ".displayCoins($link->getCoin()));
        break;
    case 'Item':
        displayItem($link);
        break;
    case 'NPCText':
        reply("This is a NPC Text with ID ".$link->getId());
        break;
    case 'MapLink':
        reply("This is a Map Link with ID ".$link->getId());
        break;
    case 'Skill':
        fetchAndDisplay($link->getId(), 'skills', 'skill', $options, "I dont know this skill, sorry.");
        break;
    case 'Trait':
        fetchAndDisplay($link->getId(), 'traits', 'trait', $options, "I dont know this trait, sorry.");
        break;
    case 'Recipe':
        fetchAndDisplay($link->getId(), 'recipes', 'recipe', $options, "I dont know this recipe, sorry.");
        break;
    case 'Wardrobe':
        fetchAndDisplay($link->getId(), 'skins', 'skin', $options, "I dont know this skin, sorry.");
        break;
    case 'Outfit':
        reply("This is an Outfit with ID ".$link->getId());
        break;
    default:
}
reply("I can't read this link (".$link->getHeader().")");

