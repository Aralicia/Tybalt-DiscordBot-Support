<?php

function db() {
    static $db = null;
    if ($db == null) {
        $config = json_decode(file_get_contents(dirname(__DIR__).'/config/db.json'));
        $db = new mysqli($config->host, $config->user, $config->password, $config->database);
    }
    return $db;
}

function getIdByName($name, $type, &$return) {
    $st = db()->prepare('SELECT api_id, name FROM entity WHERE name LIKE ? and type = ?');
    $pattern = '%'.$name.'%';
    $st->bind_param('ss', $pattern, $type);
    $st->execute();
    $st->store_result();

    $count = $st->num_rows;
    if ($count < 1 || $count > 10) {
        $st->close();
        $return = $count;
        return false;
    }
    $id = "";
    $name = "";
    $st->bind_result($id, $name);
    if ($st->num_rows > 1) {
        $entries = [];
        while($st->fetch()) {
            $entries[] = $name.' ('.$id.')';
        }
        $st->close();
        $return = $entries;
        return false;
    }
    $st->fetch();
    $st->close();
    return $id;
}
function getNameById($id, $type) {
    $name = "";
    $st = db()->prepare('SELECT api_id, name FROM entity WHERE api_id = ? and type = ?');
    $st->bind_param('is', $id, $type);
    $st->execute();
    $st->bind_result($id, $name);
    if ($st->fetch()) {
        $st->close();
        return $name;
    }
    $st->close();
    return false;
}
