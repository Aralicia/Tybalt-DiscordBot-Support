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
    echo Format::NewLine(2);

    // Fact Lines
    foreach(GW2APIFacts::getFacts($data) as $fact) {
      if (isset($fact->formated)) {
        if ($fact->requires_trait) {
          $trait = Entity::findOne([ 'types' => ['trait'], 'api_id' => $fact->requires_trait]);
          if (!empty($trait->name)) {
            echo Format::UTF8(0x2192).' '.$fact->formated.' ('.$trait->name.' - '.$fact->requires_trait.')' . Format::NewLine();
          }
        } else {
          echo '• '.$fact->formated . Format::NewLine();
        }
        if (isset($fact->overrides)) {
          foreach($fact->overrides as $ofact) {
            $trait = Entity::findOne([ 'types' => ['trait'], 'api_id' => $ofact->requires_trait]);
            if (!empty($trait->name)) {
              echo "\t".Format::UTF8(0x21B3).' '.$ofact->formated.' ('.$trait->name.' - '.$ofact->requires_trait.')' . Format::NewLine();
            }
          }
        }
      }
    }

    // Chat Link Line
    $chatLink = ChatLink::fromEntity($entity);
    echo 'Chat Link : '.$chatLink->getLink();
