<?php

function whenWasThat($time) {
    $then = new DateTime($time);
    $now = new DateTime();
    $interval = $then->diff($now);
    if ($interval->y > 0) {
        if ($interval->y == 1) {
            return '1 year ago';
        }
        return $interval->format('%y years ago');
    }
    if ($interval->m > 0) {
        if ($interval->m == 1) {
            return '1 month ago';
        }
        return $interval->format('%m months ago');
    }
    if ($interval->d > 0) {
        if ($interval->d == 1) {
            return '1 day ago';
        }
        return $interval->format('%d days ago');
    }
    if ($interval->h > 0) {
        if ($interval->h == 1) {
            return '1 hour ago';
        }
        return $interval->format('%h hours ago');
    }
    if ($interval->i > 0) {
        if ($interval->i == 1) {
            return '1 minute ago';
        }
        return $interval->format('%i minutes ago');
    }
    return 'right now';
}

function getRaidTitle($raid) {
    $id = '`#'.$raid->id.'`';
    //$comment = '*'.$raid->comment.'*'.($raid->authorServ ? ' ('.strtoupper($raid->authorServ).')' : '');
    $comment = '*'.$raid->comment.'*';
    $server = ($raid->authorServ ? strtoupper($raid->authorServ) : '');
    $authorName = $raid->authorName;
    $time = whenWasThat($raid->creationDate);
    $closed = ($raid->closed ? ' [CLOSED]' : '');
    //return 'Raid '.$id.' • '.$comment.' by '.$authorName.' '.$time.$closed;
    return 'Raid '.$id.' • **'.$server.'** • '.$comment.' by **'.$authorName.'** '.$time.$closed;
}

$lines = [];
$lines[] = getRaidTitle($raid);
$lines[] = 'Raid members :';
foreach ($members as $member) {
    $lines[] = '• '.$member->memberName.' ('.$member->comment.')';
}
reply(implode("\r\n", array_filter($lines)));
