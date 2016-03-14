<?php

public class Entity {
  
  
  /*** STATIC ***/
  public static function find($filter) {
    $whereFilters = [];
    
    if (isset($filter['query'])) {
      if (ctype_digit($val)) {
        $filter['api_id'] = $val;
      } else if (preg_match('/^\[\&.*\]$/', $code)) {
        $filter['chatlink'] = $val;
      } else {
        $filter['name_search'] = $val;
      }
    }
    
    if (isset($filter['chatlink'])) {
      $filter['api_id'] = '';
      $filter['type'] = '';
    }
    
    if (isset($filter['type'])) {
      //$whereFilters = 'type IN ()'
    }
    if (isset($filter['api_id'])) {
    }
    if (isset($filter['name'])) {
    }
    if (isset($filter['name_search'])) {
    }    
    
    $querySelect = 'SELECT *';
    $queryFrom = 'FROM entity';
    $queryWhere = (count($whereFilters) > 0 ? 'WHERE '.implode(' AND ', $whereFilters): '');
    
    $query = $querySelect.' '.$queryFrom.' '.$queryWhere;
    
    echo ($query);
  }
}