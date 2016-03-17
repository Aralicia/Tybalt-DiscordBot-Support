<?php

$argv[1] = json_encode((object)[
  'commandline' => ['!skill', 'Bloody'],
  'author' => (object)[
    'id' => 'XIDX',
    'name' => 'XNAMEX',
  ]
]);

require_once(dirname(__DIR__).'/core/Core.class.php');

$params = Command::getParams();

if (count($params) > 0) {
  $entities = Entity::find([
    'types' => ['skill', 'trait'],
    'query' => implode(' ', $params)
  ]);
  
  Response::addMessage(
    Format::CodeBlock(
      Template::load('test', ['entitites' => $entities])
    )
  );
  Response::send();
  //print_r($entities);
}

Response::addMessage("Test Message");
Response::addPrivate(Command::getAuthor()->id, "Test Private Message");
Response::addCustomData("retrieve", ["1","2","3","4","5"]);
Response::send();