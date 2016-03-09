<?php

function formatFact($fact) {
    switch($fact->type) {
        case 'Range':
            return 'Range : '.$fact->value;
            break;
        case 'Duration':
            return 'Duration : '.$fact->duration.'s';
            break;
        case 'Time':
            return $fact->text.' : '.$fact->duration.'s';
        case 'NoData':
            return $fact->text;
            break;
        case 'Damage':
            if ($fact->text != "Damage") {
                return 'Number of Hits : '.$fact->hit_count.' ('.$fact->text.')';
            } else if ($fact->hit_count > 1) {
                return 'Number of Hits : '.$fact->hit_count;
            } else {
                return null;
            }
            break;
        case 'Radius':
            return 'Radius : '.$fact->distance;
            break;
        case 'Number':
            return 'Number of Targets : '.$fact->value;
            break;
        case 'Percent':
            return $fact->text.' : '.$fact->percent.'%';
        case 'Buff':
            if (isset($fact->apply_count) && $fact->apply_count > 1) {
                return 'Applies : '.$fact->apply_count.'x '.$fact->status.($fact->duration > 0 ? ' '.$fact->duration.'s' : '');
            }
            return 'Applies : '.$fact->status.($fact->duration > 0 ? ' '.$fact->duration.'s' : '');
            break;
        case 'Recharge':
            return 'Recharge : '.$fact->value.'s';
            break;
        case 'Distance':
            return $fact->text.' : '.$fact->distance;
            break;
        case 'AttributeAdjust':
            if ($fact->target == 'Healing'){
                return 'Healing : '.$fact->value;
            }
            return '[AttributeAdjust '.$fact->target.' '.$fact->value.']';
            break;
        case 'ComboField':
            return 'Combo Field : '.$fact->field_type;
        case 'ComboFinisher':
            if (isset($fact->percent) && $fact->percent < 100) {
                return 'Combo Finisher : '.$fact->finisher_type.' ('.$fact->percent.'%)';
            }
            return 'Combo Finisher : '.$fact->finisher_type;
            break;
        case 'Final':
            break;
        case 'Unblockable':
            return 'Unblockable';
        default:
            $label = $fact->text;
            unset($fact->text, $fact->type, $fact->icon);
            return '['.$label.' : '.implode('', (array)$fact).']';
    }
    return null;
}

