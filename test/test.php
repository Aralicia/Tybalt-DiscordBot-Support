<?php

$argv[1] = json_encode((object)[
  'commandline' => ['!skill', 'Bloody'],
  'author' => (object)[
    'id' => 'XIDX',
    'name' => 'XNAMEX',
  ]
]);

require_once(dirname(__DIR__).'/core/Core.class.php');

/*
echo Command::getScript();
print_r(Command::getJsonData());
echo Command::getCommand();
print_r(Command::getParams());
print_r(Command::getAuthor());
*/

$entities = Entity::find([
  'types' => ['skill', 'trait'],
  'query' => implode(' '.Command::getParams())
]);
print_r($entities);


echo "\r\nok\r\n";