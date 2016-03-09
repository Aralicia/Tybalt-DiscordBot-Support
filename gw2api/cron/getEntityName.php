<?php

require_once(dirname(__DIR__).'/inc/database.inc.php');
require_once(dirname(__DIR__).'/inc/api.inc.php');

$id = 0;
$api_id = 0;
$type = "";
$name = "";

$db = db();
$st1 = $db->prepare('SELECT id, api_id, type FROM entity WHERE name IS NULL LIMIT 0,50');
$st2 = $db->prepare('UPDATE entity SET name = ? WHERE id = ?');
$st2->bind_param("si", $name, $id);
$st1->execute();
$st1->store_result();
$st1->bind_result($id, $api_id, $type);
while ($st1->fetch()) {
    $data = api()->v2($type.'s', ['params' => ['id' => $api_id]]);
    $name = $data->name;
    $st2->execute();
}

