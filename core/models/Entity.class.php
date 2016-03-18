<?php

class Entity {

  public $id;
  public $api_id;
  public $name; 
  public $type;

  public function __construct($id='',$api_id='',$name='',$type='') {
    $this->id = $id;
    $this->api_id = $api_id;
    $this->name = $name;
    $this->type = $type;
  }

  /*** STATIC ***/
  public static function findOne($filter) {
    $values = self::find($filter);
    if (count($values) > 0) {
      return $values[0];
    }
    return new Entity();
  }
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
      foreach($filter['types'] as $key => $type) {
        $paramTypes .= "s";
        $params[] = & $filter['types'][$key];
        $q[] = '?';
      }
      $whereFilters[] = 'type IN ('.implode(',', $q).')';
    }
    if (isset($filter['api_id'])) {
      $paramTypes .= "s";
      $params[] = & $filter['api_id'];
      $whereFilters[] = 'api_id = ?';
    }
    if (isset($filter['name'])) {
      $paramTypes .= "s";
      $params[] = & $filter['name'];
      $whereFilters[] = 'name = ?';
    }
    if (isset($filter['name_search'])) {
      $filter['name_search_like'] = '%'.$filter['name_search'].'%';
      $paramTypes .= "s";
      $params[] = & $filter['name_search_like'];
      $whereFilters[] = 'name LIKE ?';
    }    

    $querySelect = 'SELECT id, api_id, name, type';
    $queryFrom = 'FROM entity';
    $queryWhere = (count($whereFilters) > 0 ? 'WHERE '.implode(' AND ', $whereFilters): '');
    
    $query = $querySelect.' '.$queryFrom.' '.$queryWhere;

    array_unshift($params, $paramTypes);
    
    $db = Database::get();
    $st = $db->prepare($query);
    call_user_func_array([$st, 'bind_param'], $params);
    $st->execute();
    
    $resultData = ['id' => null, 'api_id' => null, 'name' => null, 'type' => null];
    $results = [];
    $st->bind_result($resultData['id'], $resultData['api_id'], $resultData['name'], $resultData['type']);
    while($st->fetch()) {
      $results[] = new Entity($resultData['id'], $resultData['api_id'], $resultData['name'], $resultData['type']);
    }
    return ($results);
  }
}
