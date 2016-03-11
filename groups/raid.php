<?php

require_once(__DIR__.'/inc/common.inc.php');
require_once(__DIR__.'/inc/database.inc.php');

function do_announce($author, $arguments) {
    $authorName = $author->name;
    $authorId = $author->id;
    $authorServ = ($author->na ? 'na' : ($author->eu ? 'eu' : ''));
    $comment = implode(' ', array_slice($arguments, 1));

    if (empty($comment)) {
        reply("Speak louder ! No one will hear you if you don't talk !");
    }

    //TODO : Add to Database

    reply('New Raid date available : '.$comment.' by @'.$authorName.(!empty($authorServ) ? ' ('.strtoupper($authorServ).')': ''));
}


$jsonRaw = '{"author":{"na":false,"eu":true,"name":"Aralicia","id":"114698444584517640"},"args":["announce","Doing","Spirit","Vale 03/11"]}';

$json = json_decode($jsonRaw);

print_r($json);

$author = $json->author;
$arguments = $json->args;

if (!isset($arguments[0])) {
    reply("Do you like raids ? I like raids. Except the raid to Claw Island.");
}

$command = $arguments[0];
$commandList = [
    'announce' => 'do_announce',
/*    'list' => '',
 *    'join' => '',
 *    'leave' => '',
 *    'close' => '',
 *    'remove' => '',
 *    'help' => '',
 */
];

if (isset($commandList[$command])) {
    call_user_func($commandList[$command], $author, $arguments);
}

reply("I can't raid this command : `".$json->args[0]."`");

