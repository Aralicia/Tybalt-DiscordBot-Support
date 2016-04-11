<?php
error_log(print_r($list, true));
echo 'Results matching `'.implode(' ', Command::getParams()).'`'.Format::NewLine();
echo '```'.Format::NewLine();
foreach($list as $item) {
  echo '- '.$item->name.' ('.$item->api_id.')'.Format::NewLine();
}
echo '```';
    
