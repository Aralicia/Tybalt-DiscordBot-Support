<?php

class Database extends mysqli{
  
  /*** SETUP ***/
  public static function setup() {
    $config = Config::get('db'); // json_decode(file_get_contents(dirname(__DIR__).'/config/db.json'));
    self::$instance = new mysqli($config->host, $config->user, $config->password, $config->database);
  }
  
  public static function get() {
    return self::$instance;
  }
  
  /*** ATTRIBUTES ***/
  private static $instance;
}
Database::setup();
