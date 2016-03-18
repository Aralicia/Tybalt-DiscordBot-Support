<?php
    // Title Line
    $specialization = Entity::find([ 'types' => ['specialization'], 'id' => $data->specialization]);
    $trait = ($data->tier == 1 ? "Adept" : ($data->tier == 2 ? "Master" : ($data->tier == 3 ? "Grandmaster" : "")))
    echo implode(' • ', array_filter([
        $data->name,
        $specialization->name,
        $trait->slot.' '.$trait.' trait'
    ]));
    echo Format::NewLine();

    // Description Line
    echo $data->description;
    echo Format::NewLine();

    // Fact Lines
    foreach(GW2APIFacts::getFacts($data) as $fact) {
        if (isset($fact->formated)) {
            echo '• '.$fact->formated.Format::NewLine();
        }
    }