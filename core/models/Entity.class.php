<?php

class Entity {
  
  /*** STATIC ***/
  public static function find($filter) {
    $whereFilters = [];
    $paramTypes = "";
    $params = [];
    
    if (isset($filter['query'])) {
      if (ctype_digit($filter['query'])) {
        $filter['api_id'] = $filter['query'];
      } else if (preg_match('/^\[\&.*\]$/', $filter['query'])) {
        $filter['chatlink'] = $filter['query'];
      } else {
        $filter['name_search'] = $filter['query'];
      }
    }

    if (isset($filter['chatlink'])) {
      $chatlink = new ChatLink($filter['chatlink']);
      if (in_array($chatlink->getType(), ['Skill', 'Trait', 'Recipe', 'Wardrobe'])) {
        $filter['api_id'] = $chat->getId();
        $filter['types'] = array_intersect($filter['types'], [strtolower($chatlink->getType())]);
      } else if ($chatlink->getType() == 'Item') {
        $filter['api_id'] = $chat->getItemData()['item_id'];
        $filter['types'] = array_intersect($filter['types'], ['item']);
      } else {
        $filter['name_search'] = $filter['chatlink'];
      }
    }

    if (isset($filter['types'])) {
      $q = [];
      foreach($filter['types'] as $type) {
        $paramTypes .= "s";
        $params[] = $type;
        $q[] = '?';
      }
      $whereFilters[] = 'type IN ('.implode(',', $q).')';
    }
    if (isset($filter['api_id'])) {
      $paramTypes .= "s";
      $params[] = $filter['api_id'];
      $whereFilters[] = 'api_id = ?';
    }
    if (isset($filter['name'])) {
      $paramTypes .= "s";
      $params[] = $filter['name'];
      $whereFilters[] = 'name = ?';
    }
    if (isset($filter['name_search'])) {
      $paramTypes .= "s";
      $params[] = '%'.$filter['name_search'].'%';
      $whereFilters[] = 'name LIKE = ?';
    }    

    $querySelect = 'SELECT *';
    $queryFrom = 'FROM entity';
    $queryWhere = (count($whereFilters) > 0 ? 'WHERE '.implode(' AND ', $whereFilters): '');
    
    $query = $querySelect.' '.$queryFrom.' '.$queryWhere;

    
    echo ($query);
    echo ($paramTypes);
    print_r($params);
  }
}
