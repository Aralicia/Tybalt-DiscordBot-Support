<?php

/*
$argv[1] = json_encode((object)[
  'commandline' => ['!skill', 'Bloody'],
  'author' => (object)[
    'id' => 'XIDX',
    'name' => 'XNAMEX',
  ]
]);
 */

require_once(dirname(__DIR__).'/core/Core.class.php');

Template::setTemplatePath(__DIR__.'/templates/');

$params = Command::getParams();
$verbose = Command::getOption(['verbose', 'v']);

if (count($params) > 0) {
  $entities = Entity::find([
    'types' => ['skill', 'trait'],
    'query' => implode(' ', $params)
  ]);
  
  $count = count($entities);
  if ($count < 1) {
    Response::addMessage("I've not found anything, sorry.");
  } else if ($count > 10 && $verbose == false) {
    Response::addMessage("I've found ".$count." matches. Can you be more precise ?");
  } else if ($count > 3) {
    Response::addMessage("I've found the following skills and traits : `".implode('`, `', array_map(function($entity) {
      return $entity->name.' ('.$entity->api_id.')';
    }, $entities))."`. Which one do you want ?");
  } else {
    foreach($entities as $entity) {
      $data = GW2API::v2(
        ($entity->type == 'skill' ? 'skills' : 'traits'),
        ['params' => ['id' => $entity->api_id]]
      );
      Response::addMessage(
        Format::CodeBlock(Template::load($entity->type, ['entity' => $entity, 'data' => $data]))
      );
    }
  }
  Response::send();
}

Response::addMessage('Yes, I have skills, thank you.');
Response::send();
