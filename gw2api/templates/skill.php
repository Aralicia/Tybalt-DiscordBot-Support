<?php

require_once(dirname(__DIR__).'/inc/gw2_facts.inc.php');
require_once(dirname(__DIR__).'/inc/gw2_chatlink.inc.php');

function getSkillTitle($skill, $verbose) {
    $return = $skill->name.' (';
    $profession = "";
    $type = "";
    $categories = "";
    $traited = false;
    if (count($skill->professions) == 1) {
        $profession = $skill->professions[0];
    }
    if ($skill->type == "Weapon") {
        $slot_num = substr($skill->slot, -1);
        if ($profession == "Elementalist") {
            $type = $skill->weapon_type.' '.$slot_num.' ('.$skill->attunement.')';
        } else if ($profession == "Thief") {
            if ($slot_num == '3' && isset($skill->dual_wield)) {
                $type = $skill->weapon_type.'/'.$skill->dual_wield.' '.$slot_num;
            } else {
                $type = $skill->weapon_type.' '.$slot_num;
            }
        } else if ($skill->weapon_type == 'None') {
            $type = 'Downed '.$slot_num;
        } else {
            $type = $skill->weapon_type.' '.$slot_num;
        }
    } else {
        $type = $skill->type;
    }
    if (isset($skill->categories) && count($skill->categories) > 0) {
        $categories = ' ['.implode(', ', $skill->categories).']';
    }
    return $skill->name.' - '.(empty($profession) ? '': $profession.' ').$type.$categories;
}
function getAllFacts($skill) {
    $baseFacts = (isset($skill->facts) ? $skill->facts : []);
    $traitedFacts = (isset($skill->traited_facts) ? $skill->traited_facts : []);

    foreach($traitedFacts as $tfact) {
        if (isset($tfact->overrides)) {
            if (!isset($baseFacts[$tfact->overrides]->overrides)) {
                $baseFacts[$tfact->overrides]->overrides = [];
            }
            $baseFacts[$tfact->overrides]->overrides[] = $tfact;
        } else {
            $baseFacts[] = $tfact;
        }
    }
    return $baseFacts;
}

function formatComplexFact($fact, $verbose) {
    if (isset($fact->requires_trait)) {
        if (!$verbose) return null;
        return html_entity_decode('&#8594;').' '.formatFact($fact).' ('.getNameById($fact->requires_trait, 'trait').' - '.$fact->requires_trait.')';
    } else if (isset($fact->overrides) && $verbose){
        $base = formatFact($fact);
        if ($base == null) {
            return null;
        }
        $overrides = [];
        foreach($fact->overrides as $ofact) {
            $overrides[] = "\r\n\t".html_entity_decode('&#8627;').' '.formatFact($ofact).' ('.getNameById($ofact->requires_trait, 'trait').' - '.$ofact->requires_trait.')';
        }
        return $base.implode('', $overrides);
    }
    return formatFact($fact);
}

$verbose = (in_array('v', $options) || in_array('verbose', $options));
$debug = (in_array('d', $options) || in_array('debug', $options));

if ($debug) {
    reply(print_r($data));
}

$lines = [];
$lines[] = getSkillTitle($data, $verbose);
$lines[] = $data->description;
if (isset($data->cost)) {
    $lines[] = 'Energy Cost : '.$data->cost;
}
if (isset($data->initiative)) {
    $lines[] = 'Initiative Cost : '.$data->cost;
}
foreach(getAllFacts($data) as $fact) {
    $lines[] = formatComplexFact($fact, $verbose);
}

if ($verbose) {
    if (isset($data->flip_skill)) {
        if (in_array('Gadget', $data->categories)) {
            $lines[] = 'Overcharge : '.getNameById($data->flip_skill, 'skill').' ('.$data->flip_skill.')';
        } else {
            $lines[] = 'Flip To : '.getNameById($data->flip_skill, 'skill').' ('.$data->flip_skill.')';
        }
    }
    if (isset($data->transform_skills) && !empty($data->transform_skills)) {
        $skillList = array_map(function($val) {
            $name = getNameById($val, 'skill');
            if ($name == false) return null;
            return $name.' ('.$val.')';
        }, $data->transform_skills);
        $lines[] = 'Transformed Skills : '.implode(', ', array_filter($skillList));
    }
    if (isset($data->bundle_skills) && !empty($data->bundle_skills)) {
        $skillList = array_map(function($val) {
            if ($name == false) return null;
            return $name.' ('.$val.')';
        }, $data->bundle_skills);
        $lines[] = 'Bundle Skills : '.implode(', ', array_filter($skillList));
    }
    if (isset($data->toolbelt_skill)) {
        $lines[] = 'Toolbelt Skill : '.getNameById($data->toolbelt_skill, 'skill').' ('.$data->toolbelt_skill.')';
    }
}
$lines[] = 'Chat Link : '.$data->chat_link;
reply(implode("\r\n", array_filter($lines)), true);
