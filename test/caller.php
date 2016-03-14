<?php

$params = json_encode((object)[
  'commandline' => ['!skill', 'Bloody'],
  'author' => (object)[
    'id' => 'XIDX',
    'name' => 'XNAMEX',
  ]
]);

$command = 'php test.php "'.addcslashes($params, "\"\\").'"';
echo ($command);
$result = exec($command);
echo($result);

//php test.php "{`"commandline`":[`"!skill`",`"Bloody`"],`"author`":{`"id`":`"XIDX`",`"name`":`"XNAMEX`"}}"