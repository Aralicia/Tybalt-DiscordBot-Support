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
        echo Format::NewLine();
        echo '• Energy Cost : '.$data->cost;
    }
    if (isset($data->initiative)) {
        echo Format::NewLine();
        echo '• Initiative Cost : '.$data->cost;
    }
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
    
    // Flip Skill Line
    if (isset($data->flip_skill)) {
        $flip_skill = Entity::findOne([ 'types' => ['skill'], 'api_id' => $data->flip_skill]);
        if (in_array('Gadget', $data->categories)) {
            echo Format::NewLine();
            echo '• Overcharge : '.$flip_skill->name.' ('.$data->flip_skill.')';
        } else {
            echo Format::NewLine();
            echo '• Flip To : '.$flip_skill->name.' ('.$data->flip_skill.')';
        }
    }
    
    // Transform Skills Line
    if (isset($data->transform_skills) && !empty($data->transform_skills)) {
        $skillList = array_map(function($val) {
            $skill = Entity::findOne([ 'types' => ['skill'], 'api_id' => $val]);
            if ($skill == null) return null;
            return $skill->name.' ('.$val.')';
        }, $data->transform_skills);
        echo Format::NewLine();
        echo '• Transformed Skills : '.implode(', ', array_filter($skillList));
    }
    
    // Bundle Skills Line
    if (isset($data->bundle_skills) && !empty($data->bundle_skills)) {
        $skillList = array_map(function($val) {
            $skill = Entity::findOne([ 'types' => ['skill'], 'api_id' => $val]);
            if ($skill == null) return null;
            return $skill->name.' ('.$val.')';
        }, $data->bundle_skills);
        echo Format::NewLine();
        echo '• Bundle Skills : '.implode(', ', array_filter($skillList));
    }
    
    // Toolbelt Skill Line
    if (isset($data->toolbelt_skill)) {
        $toolbelt_skill = Entity::findOne([ 'types' => ['skill'], 'api_id' => $data->toolbelt_skill]);
        echo Format::NewLine();
        echo '• Toolbelt Skill : '.$toolbelt_skill->name.' ('.$data->toolbelt_skill.')';
    }
