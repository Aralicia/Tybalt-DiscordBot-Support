<?php

require_once(dirname(__DIR__).'/inc/database.inc.php');
require_once(dirname(__DIR__).'/inc/api.inc.php');

$types = ['skill', 'trait', 'item', 'recipe'];

$entity = "";
$type = "";
$db = db();
$st = $db->prepare('INSERT IGNORE INTO entity (api_id, name, type) VALUES (?, NULL, ?)');
$st->bind_param('ss', $entity, $type);


foreach($types as $type) {
    $entities = api()->v2($type.'s');
    foreach($entities as $entity) {
        $st->execute();
    }
}
$st->close();

