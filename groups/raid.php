<?php

require_once(__DIR__.'/inc/common.inc.php');
require_once(__DIR__.'/inc/database.inc.php');


$args = parseArgs();
$options = $args['options'];
$splits = $args['splits'];
if (!isset($splits[0])) {
    reply("Do you like raids ? I like raids. Except the raid to Claw Island.");
}
switch($splits[0])) {
    case "announce":
        // todo
        break;
    case "list":
        // todo
        break;
    case "join":
        // todo
        break;
    case "leave":
        // todo
        break;
    case "close":
        // todo
        break;
    case "remove":
        // todo
        break;
    default:
}

reply("I can't raid this command : `".$splits[0]."`");

