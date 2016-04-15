<?php

require_once(dirname(__DIR__).'/core/Core.class.php');

Template::setTemplatePath(__DIR__.'/templates/');

$params = Command::getParams();
$verbose = Command::getOption(['verbose', 'v']);
$list = Command::getOption(['list', 'l']);

if (count($params) > 0) {
  $entities = Entity::find([
    'types' => ['item'],
    'query' => $params
  ]);
  
  $count = count($entities);
  $merge_param = implode(' ', $params);
  if ($count < 1) {
    Response::addMessage("I've not found anything, sorry.");
  } else if ($count > 50) {
    Response::addMessage("I've found ".$count." matches. Can you be more precise ?");
  } else if ($list) {
    Response::addMessage(
      Template::load('itemlist', ['list' => $entities])
    );
  } else {
    $first = true;
    while (count($entities) > 0) {
      $entity = array_shift($entities);
      if ($entity->getDistance($merge_param) > 0 && !$first) {
        array_unshift($entities, $entity);
        break;
      }
      $data = GW2API::v2('items', ['params' => ['id' => $entity->api_id]]);
      Response::addMessage(
        Format::CodeBlock(Template::load('item', ['entity' => $entity, 'data' => $data]))
      );
      $first = false;
    }
    $count = count($entities);
    if ($count > 0) {
      Response::addMessage("I've found ".count($entities)." additional partial matches. You can get a list with !item --list ".$merge_param);
    }
  }
  Response::send();
}

Response::addMessage('Apples, Apples for sale, come and get a nice quiet apple!');
Response::send();
