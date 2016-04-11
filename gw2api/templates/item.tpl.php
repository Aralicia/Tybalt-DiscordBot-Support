<?php

    if (!function_exists('formatCoin')) {
      function formatCoin($value) {
        $gp = floor($value/10000);
        $sp = floor(($value - ($gp*10000))/100);
        $cp = $value - ($sp*100) - ($gp*10000);
        if ($gp > 0) {
          return $gp.'gp '.$sp.'sp '.$cp.'cp';
        }
        if ($sp > 0) {
          return $sp.'sp '.$cp.'cp';
        }
        return $cp.'cp';
      }
    }
    if (!function_exists('addSpaceToUpper')) {
      function addSpaceToUpper($string) {
        return trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $string));
      }
    }

    $verbose = Command::getOption(['verbose', 'v']);
    $icon = Command::getOption(['icon', 'i']);

    // Title Line
    echo implode(' • ', array_filter([
      $data->name.' ('.$data->id.')',
      'level '.$data->level.' '.$data->rarity.' '.addSpaceToUpper($data->type),
      (isset($data->game_types) ? '['.implode(',', $data->game_types).']' : null )
    ]));
    echo Format::NewLine();
    if ($verbose) print_r($data);

    // Flags Line
    if (isset($data->flags) && count($data->flags) > 0) {
      echo implode(', ', array_filter(array_map('addSpaceToUpper', $data->flags)));
      echo Format::NewLine();
    }

    // Description Line
    if (isset($data->description) && !empty($data->description)) {
      echo Format::NewLine();
      echo strip_tags(str_replace('<br>', Format::NewLine(), $data->description));
      echo Format::NewLine();
    }

    // bonuses
    if (isset($data->details)) {
      echo Format::NewLine();
      if (isset($data->details->type)) {
        echo(addSpaceToUpper($data->type).' Type : '.$data->details->type);
        echo Format::NewLine();
      }
      if (isset($data->details->description) && !empty($data->details->description)) {
        echo $data->details->description;
        echo Format::NewLine();
      }
      if (isset($data->details->bonuses) && count($data->details->bonuses) > 0) {
        foreach($data->details->bonuses as $key => $value) {
          echo '('.($key+1).'): '.strip_tags($value).Format::NewLine();
        }
      }
      if (isset($data->details->infix_upgrade->buff->description)) {
        echo $data->details->infix_upgrade->buff->description;
        echo Format::NewLine();
      }
      if (isset($data->details->duration_ms)) {
        echo 'Duration : '.($data->details->duration_ms/1000).'s';
        echo Format::NewLine();
      }
    }

    // Vendor Value Line
    if ($data->vendor_value > 0) {
      echo Format::NewLine();
      echo 'Vendor Value : '.formatCoin($data->vendor_value);
    }

    /*
    // Fact Lines
    foreach(GW2APIFacts::getFacts($data) as $fact) {
      if (isset($fact->formated)) {
        if (isset($fact->requires_trait) && $verbose) {
          $trait = Entity::findOne([ 'types' => ['trait'], 'api_id' => $fact->requires_trait]);
          if (!empty($trait->name)) {
            echo Format::UTF8(0x2192).' '.$fact->formated.' ('.$trait->name.' - '.$fact->requires_trait.')' . Format::NewLine();
          }
        } else {
          echo '• '.$fact->formated . Format::NewLine();
        }
        if (isset($fact->overrides) && $verbose) {
          foreach($fact->overrides as $ofact) {
            $trait = Entity::findOne([ 'types' => ['trait'], 'api_id' => $ofact->requires_trait]);
            if (!empty($trait->name)) {
              echo "\t".Format::UTF8(0x21B3).' '.$ofact->formated.' ('.$trait->name.' - '.$ofact->requires_trait.')' . Format::NewLine();
            }
          }
        }
      }
    }
     */

    // Icon Line
    if ($icon) {
      echo Format::NewLine();
      echo 'Icon : '.$data->icon;
    }

    // Chat Link Line
    echo Format::NewLine();
    $chatLink = ChatLink::fromEntity($entity);
    echo 'Chat Link : '.$chatLink->getLink();
    
