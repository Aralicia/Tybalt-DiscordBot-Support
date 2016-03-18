<?php
    // Title Line
    $specialization = Entity::findOne([ 'types' => ['specialization'], 'api_id' => $data->specialization]);
    $trait = ($data->tier == 1 ? "Adept" : ($data->tier == 2 ? "Master" : ($data->tier == 3 ? "Grandmaster" : "")));
    echo implode(' • ', array_filter([
        $data->name.' ('.$data->id.')',
        $specialization->name,
        $data->slot.' '.$trait.' trait'
    ]));
    echo Format::NewLine();

    // Description Line
    echo $data->description;
    echo Format::NewLine();

    // Fact Lines
    foreach(GW2APIFacts::getFacts($data) as $fact) {
      if (isset($fact->formated)) {
        if ($fact->requires_trait) {
          $trait = Entity::findOne([ 'types' => ['trait'], 'api_id' => $fact->requires_trait]);
          if (!empty($trait->name)) {
            echo Format::NewLine();
            echo Format::UTF8(0x2192).' '.$fact->formated.' ('.$trait->name.' - '.$fact->requires_trait.')';
          }
        } else {
          echo Format::NewLine();
          echo '• '.$fact->formated;
        }
        if (isset($fact->overrides)) {
          foreach($fact->overrides as $ofact) {
            $trait = Entity::findOne([ 'types' => ['trait'], 'api_id' => $ofact->requires_trai]);
            if (!empty($trait->name)) {
              echo Format::NewLine()."\t";
              echo utf(0x21B3).' '.$ofact->formated.' ('.$trait->name.' - '.$ofact->requires_trait.')';
            }
          }
        }
      }
    }
