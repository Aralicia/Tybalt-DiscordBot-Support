<?php

$jsonRaw = $argv['1'];
//$jsonRaw = '{"author":{"na":false,"eu":true,"name":"Aralicia","id":"114698444584517640"},"args":["announce","Doing","Spirit Vale","03/11 '.uniqid().'"]}';
//$jsonRaw = '{"author":{"na":false,"eu":true,"name":"Aralicia","id":"114698444584517640"},"args":["list"]}';
//$jsonRaw = '{"author":{"na":false,"eu":true,"name":"Aralicia","id":"114698444584517640"},"args":["list", "2"]}';
//$jsonRaw = '{"author":{"na":false,"eu":true,"name":"Aralicia","id":"114698444584517640"},"args":["close", "3"]}';
//$jsonRaw = '{"author":{"na":false,"eu":true,"name":"Aralicia","id":"114698444584517640"},"args":["open", "2"]}';
//$jsonRaw = '{"author":{"na":false,"eu":true,"name":"Aralicia","id":"114698444584517640"},"args":["remove", "2"]}';
//$jsonRaw = '{"author":{"na":false,"eu":true,"name":"Aralicia","id":"114698444584517640"},"args":["join","2","Thief or Elem"]}';
//$jsonRaw = '{"author":{"na":false,"eu":true,"name":"Aralicia","id":"114698444584517640"},"args":["leave","2"]}';
//$jsonRaw = '{"author":{"na":false,"eu":true,"name":"Aralicia","id":"114698444584517640"},"args":["call","2"]}';



require_once(__DIR__.'/inc/common.inc.php');
require_once(__DIR__.'/inc/database.inc.php');

function can_edit($author, $raidAuthor) {
    if ($author->id == $raidAuthor) {
        return true;
    }
    if (in_array($author->id, ['114698444584517640', '124356319728762883'])) {
        return true;
    }
    return false;
}

function do_announce($author, $arguments) {
    $authorName = $author->name;
    $authorId = $author->id;
    $authorServ = ($author->na ? 'na' : ($author->eu ? 'eu' : ''));
    $comment = implode(' ', array_slice($arguments, 1));
    $raidId = null;

    if (empty($comment)) {
        reply("Speak louder ! No one will hear you if you don't talk !");
    }

    $st = db()->prepare("SELECT MIN(t1.raid_id + 1) AS nextID FROM group_raid t1 LEFT JOIN group_raid t2 ON t1.raid_id + 1 = t2.raid_id WHERE t2.raid_id IS NULL");
    $st->execute();
    $st->bind_result($raidId);
    $st->fetch();
    $st->close();

    if ($raidId == null) $raidId = 1;

    $st = db()->prepare("INSERT INTO group_raid (raid_id, author_id, author_name, author_serv, comment, creation_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $st->bind_param('issss', $raidId, $authorId, $authorName, $authorServ, $comment);
    $st->execute();
    echo ($st->error);
    $st->close();
    
    reply('New Raid date available : '.$comment.' by @'.$authorName.(!empty($authorServ) ? ' ('.strtoupper($authorServ).')': ''));
}
function do_list($author, $arguments) {
    if (count($arguments) == 2 && isId($arguments[1])) {
        // list specific raid
        $st = db()->prepare('SELECT raid_id, author_id, author_name, author_serv, comment, creation_date, closed FROM group_raid WHERE raid_id = ?');
        $st->bind_param('i', intval($arguments[1]));
        $st->execute();
        $st->store_result();
        $count = $st->num_rows;
        if ($count < 1) {
            $st->close();
            reply('I don\'t know this raid.');
        }

        $raid = null;
        $members = [];
        $raidId = null;
        $authorName = null;
        $authorName = null;
        $authorServ = null;
        $comment = null;
        $creationDate = null;
        $closed = null;

        $st->bind_result($raidId, $authorId, $authorName, $authorServ, $comment, $creationDate, $closed);
        $st->fetch();
        $raid = (object)[
            'id' => $raidId,
            'authorId' => $authorId,
            'authorName' => $authorName,
            'authorServ' => $authorServ,
            'comment' => $comment,
            'creationDate' => $creationDate,
            'closed' => $closed,
        ];
        $st->close();

        $st = db()->prepare('SELECT member_id, member_name, comment FROM group_raid_member WHERE raid_id = ?');
        $st->bind_param('i', intval($arguments[1]));
        $st->execute();
        $st->bind_result($authorId, $authorName, $comment);
        while ($st->fetch()) {
            $members[] = (object)[
                'memberId' => $authorId,
                'memberName' => $authorName,
                'comment' => $comment
            ];
        }


        $st->close();
        include(__DIR__.'/templates/raid_details.php');
    } else {
        // list mathcing raids
        $filter = '%'.implode(' ', array_slice($arguments, 1)).'%';
        $membersCountSelect = '(SELECT COUNT(*) FROM group_raid_member grm WHERE grm.raid_id = group_raid.raid_id) as Q';
        //$st = db()->prepare('SELECT raid_id, author_name, author_serv, comment, creation_date, closed FROM group_raid WHERE comment LIKE ? ORDER BY raid_id ASC');
        $st = db()->prepare('SELECT raid_id, author_name, author_serv, comment, creation_date, closed, '.$membersCountSelect.' FROM group_raid WHERE comment LIKE ? ORDER BY raid_id ASC');
        $st->bind_param('s', $filter);
        $st->execute();
        $st->store_result();

        $count = $st->num_rows;
        if ($count < 1) {
            $st->close();
            reply('No raid matches your filter.');
        }

        $raids = [];
        $raidId = null;
        $authorName = null;
        $authorServ = null;
        $comment = null;
        $creationDate = null;
        $closed = null;
        $membersCounter = 0;

        //$st->bind_result($raidId, $authorName, $authorServ, $comment, $creationDate, $closed);
        $st->bind_result($raidId, $authorName, $authorServ, $comment, $creationDate, $closed, $membersCounter);
        while($st->fetch()) {
            $raids[] = (object)[
                'id' => $raidId,
                'authorName' => $authorName,
                'authorServ' => $authorServ,
                'comment' => $comment,
                'creationDate' => $creationDate,
                'closed' => $closed,
                'membersCounter' => $membersCounter
            ];
        }
        $st->close();
        include(__DIR__.'/templates/raid_list.php');
    }
}
function do_edit($author, $arguments) {
    $remove = ($arguments[0] == 'remove');
    $close = ($arguments[0] == 'close');
    $open = ($arguments[0] == 'open');

    if (!(count($arguments) == 2 && isId($arguments[1]))) {
        reply('I can\'t '.$arguments[0].' that !');
    }
    
    $st = db()->prepare('SELECT ID, raid_id, author_id, author_name, author_serv, comment, creation_date, closed FROM group_raid WHERE raid_id = ?');
    $st->bind_param('i', intval($arguments[1]));
    $st->execute();
    $st->store_result();

    $count = $st->num_rows;
    if ($count != 1) {
        $st->close();
        reply('No raid matches this id.');
    }

    $entryId = null;
    $raidId = null;
    $authorId = null;
    $authorName = null;
    $authorServ = null;
    $comment = null;
    $creationDate = null;
    $closed = null;

    $st->bind_result($entryId, $raidId, $authorId, $authorName, $authorServ, $comment, $creationDate, $closed);
    $st->fetch();
    $st->close();
    
    if (!can_edit($author, $authorId)) {
        reply('You can\'t '.$arguments[0].' this raid');
    }
    if ($closed && $close) {
        reply('This raid is already closed !');
    }
    if (!$closed && $open) {
        reply('This raid is already open !');
    }
    if ($remove) {
        $st = db()->prepare('DELETE FROM group_raid WHERE ID = ?');
        $st->bind_param('i', $entryId);
        $st->execute();
        $st->close();
        
        $st = db()->prepare('DELETE FROM group_raid_member WHERE raid_id = ?');
        $st->bind_param('i', $raidId);
        $st->execute();
        $st->close();
        reply('Raid '.$raidId.' ('.$comment.') by '.$authorName.' has been removed !');
    }
    $st = db()->prepare('UPDATE group_raid SET closed = ? WHERE ID = ?');
    $st->bind_param('ii', $close, $entryId);
    $st->execute();
    $st->close();
    if ($close) {
        reply('Raid '.$raidId.' ('.$comment.') by '.$authorName.' is now closed !');
    }
    reply('Raid '.$raidId.' ('.$comment.') by '.$authorName.' is now open !');
}

function do_join($author, $arguments) {
    if (!(count($arguments) > 1 && isId($arguments[1]))) {
        reply('I can\'t find that !');
    }
    $id = intval($arguments[1]);

    $st = db()->prepare('SELECT raid_id, author_serv, comment, closed FROM group_raid WHERE raid_id = ?');
    $st->bind_param('i', $id);
    $st->execute();
    $st->store_result();

    $count = $st->num_rows;
    if ($count != 1) {
        $st->close();
        reply('No raid matches this id.');
    }

    $raidId = null;
    $authorServ = null;
    $raidComment = null;
    $closed = null;

    $st->bind_result($raidId, $authorServ, $raidComment, $closed);
    $st->fetch();
    $st->close();

    if ($closed) {
        reply('This raid is closed.');
    }
    if ($authorServ == 'eu' && (!$author->eu && $author->na)) {
        reply('We don\'t do cross-continental raids. Yet.');
    }
    if ($authorServ == 'na' && (!$author->na && $author->eu)) {
        reply('We don\'t do cross-continental raids. Yet.');
    }

    $memberId = $author->id;
    $memberName = $author->name;
    $comment =  implode(' ', array_slice($arguments, 2));
    $st = db()->prepare('INSERT INTO group_raid_member (raid_id, member_id, member_name, comment) VALUES (?, ?, ?, ?)');
    $st->bind_param('isss', $raidId, $memberId, $memberName, $comment);
    $st->execute();
    if ($st->errno != 0) {
        reply('You already are a member of this Raid !');
    }
    reply($memberName.' just joined Raid *'.$raidComment.'* : '.$comment);
    
}
function do_leave($author, $arguments) {
    if (!(count($arguments) > 1 && isId($arguments[1]))) {
        reply('I can\'t find that !');
    }
    $id = intval($arguments[1]);


    $st = db()->prepare('SELECT raid_id, author_name, comment FROM group_raid WHERE raid_id = ?');
    $st->bind_param('i', $id);
    $st->execute();
    $st->store_result();

    $count = $st->num_rows;
    if ($count != 1) {
        $st->close();
        reply('No raid matches this id.');
    }

    $raidId = null;
    $authorName = null;
    $raidComment = null;
    $entryID = null;

    $st->bind_result($raidId, $authorName, $raidComment);
    $st->fetch();
    $st->close();

    $st = db()->prepare('SELECT ID FROM group_raid_member WHERE raid_id = ? AND member_id = ?');
    $st->bind_param('is', $raidId, $author->id);
    $st->execute();
    $st->store_result();
    $count = $st->num_rows;
    if ($count < 1) {
        $st->close();
        reply('You aren\'t a member of this raid.');
    }
    $st->bind_result($entryID);
    $st->fetch();
    $st->close();
    
    $st = db()->prepare('DELETE FROM group_raid_member WHERE raid_id = ?');
    $st->bind_param('i', $raidId);
    $st->execute();
    $st->close();
    reply($author->name.' just left the raid *'.$raidComment.'*.');
}

function do_call($author, $arguments) {
    if (!(count($arguments) > 1 && isId($arguments[1]))) {
        reply('I can\'t find that !');
    }
    reply('Debugging: '.$arguments[2]);
    reply('I can only reply once.');
    $account = '';
    if ($arguments[2]){
        $account = $arguments[2];
    }
    
    $id = intval($arguments[1]);        
    $st = db()->prepare('SELECT raid_id, author_id FROM group_raid WHERE raid_id = ?');
    $st->bind_param('i', $id);
    $st->execute();
    $st->store_result();

    $count = $st->num_rows;
    if ($count != 1) {
        $st->close();
        reply('No raid matches this id.');
    }

    $raidId = null;
    $authorId = null;
    $members = [];

    $st->bind_result($raidId, $authorId);
    $st->fetch();
    $st->close();

    if (!can_edit($author, $authorId)) {
        reply('You can\'t call this raid');
    }

    $st = db()->prepare('SELECT member_id FROM group_raid_member WHERE raid_id = ?');
    $st->bind_param('i', $id);
    $st->execute();
    $st->bind_result($authorId);
    while ($st->fetch()) {
        $members[] = '<@'.$authorId.'>';
        /*
        if ($account){
            $msg = '`/squadjoin '.$account.'`';
            reply('More debugging: '.$msg.' # '.$authorId);
            send_message($authorId, $msg);
        }
        */
    }
    $st->close();
    if ($account){
        $msg = '`/squadjoin '.$account.'`';
        //reply('More debugging: '.$msg.' # '.$authorId);
        reply($msg, false, $members);
    }    
    reply('**RAID TIME !**'."\r\n".implode(', ', $members));
// <@ID>
}

$json = json_decode($jsonRaw);

$author = $json->author;
$arguments = $json->args;

if (!isset($arguments[0])) {
    reply("Do you like raids ? I like raids. Except the raid to Claw Island.");
}

$command = $arguments[0];
$commandList = [
    'announce' => 'do_announce',
    'list' => 'do_list',
    'open' => 'do_edit',
    'close' => 'do_edit',
    'remove' => 'do_edit',
    'join' => 'do_join',
    'leave' => 'do_leave',
    'call' => 'do_call'
/*    'join' => '',
 *    'leave' => '',
 *    'help' => '',
 */
];

if (isset($commandList[$command])) {
    call_user_func($commandList[$command], $author, $arguments);
}

reply("I can't raid this command : `".$json->args[0]."`");

