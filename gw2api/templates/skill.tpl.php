<?php
    // Title Line
    $profession = (count($data->professions) == 1 ? $data->professions[0] : null);
    $slot_num = substr($data->slot, -1);
    $type = ($data->type == "Weapon" ? 
        ($data->weapon_type == 'None' ?
            'Downed '.$slot_num :
        ($profession == "Elementalist" ? 
            $data->weapon_type.' '.$slot_num.' ('.$data->attunement.')' :
        ($profession == "Thief" && $slot_num == '3' && isset($data->dual_wield) ?
            $data->weapon_type.'/'.$data->dual_wield.' '.$slot_num : 
            $data->weapon_type.' '.$slot_num
    ))) : $data->type);
    $categories = (isset($skill->categories) && count($skill->categories) > 0 ? implode(', ', $data->categories) : null);
    
    echo implode(' • ', array_filter([
        $data->name,
        $profession,
        $type,
        $categories
    ]));
    echo Format::NewLine();

    // Description Line
    echo $data->description;
    echo Format::NewLine();
    
    // Energy & Initiative Lines
    if (isset($data->cost)) {
        echo '• Energy Cost : '.$data->cost . Format::NewLine();
    }
    if (isset($data->initiative)) {
        echo '• Initiative Cost : '.$data->cost . Format::NewLine();
    }
    // Fact Lines
    foreach(GW2APIFacts::getFacts($data) as $fact) {
        if (isset($fact->formated)) {
            echo '• '.$fact->formated . Format::NewLine();
        }
    }
    
    // Flip Skill Line
    if (isset($data->flip_skill)) {
        $flip_skill = Entity::find([ 'types' => ['specialization'], 'id' => $data->flip_skill]);
        if (in_array('Gadget', $data->categories)) {
            echo '• Overcharge : '.$flip_skill->name.' ('.$data->flip_skill.')' . Format::NewLine();
        } else {
            echo '• Flip To : '.$flip_skill->name.' ('.$data->flip_skill.')' . Format::NewLine();
        }
    }
    
    // Transform Skills Line
    if (isset($data->transform_skills) && !empty($data->transform_skills)) {
        $skillList = array_map(function($val) {
            $skill = Entity::find([ 'types' => ['skill'], 'id' => $val]);
            if ($skill == null) return null;
            return $skill->name.' ('.$val.')';
        }, $data->transform_skills);
        echo '• Transformed Skills : '.implode(', ', array_filter($skillList)) . Format::NewLine();
    }
    
    // Bundle Skills Line
    if (isset($data->bundle_skills) && !empty($data->bundle_skills)) {
        $skillList = array_map(function($val) {
            $skill = Entity::find([ 'types' => ['skill'], 'id' => $val]);
            if ($skill == null) return null;
            return $skill->name.' ('.$val.')';
        }, $data->bundle_skills);
        echo '• Bundle Skills : '.implode(', ', array_filter($skillList)) . Format::NewLine();
    }
    
    // Toolbelt Skill Line
    if (isset($data->toolbelt_skill)) {
        $toolbelt_skill = Entity::find([ 'types' => ['specialization'], 'id' => $data->toolbelt_skill]);
        echo '• Toolbelt Skill : '.$toolbelt_skill->name.' ('.$data->toolbelt_skill.')' . Format::NewLine();
    }